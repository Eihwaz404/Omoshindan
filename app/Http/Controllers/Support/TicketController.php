<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\StoreTicketRequest;
use App\Models\SupportArea;
use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $tickets = Ticket::query()
            ->with(['requester', 'assignedTo', 'area'])
            ->visibleTo($user)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('area'), fn ($query) => $query->where('area_id', $request->integer('area')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->string('search'));

                $query->where(function ($query) use ($search) {
                    $query->where('subject', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('support.tickets.index', [
            'tickets' => $tickets,
            'areas' => SupportArea::query()->active()->orderBy('name')->get(),
            'statuses' => config('support.statuses', []),
            'isTechnical' => $user->isTechnical(),
            'filters' => [
                'status' => (string) $request->string('status'),
                'area' => (string) $request->string('area'),
                'search' => trim((string) $request->string('search')),
            ],
        ]);
    }

    public function create(): View
    {
        return view('support.tickets.create', [
            'ticket' => new Ticket([
                'status' => Ticket::STATUS_OPEN,
            ]),
            'areas' => SupportArea::query()->active()->orderBy('name')->get(),
            'statuses' => config('support.statuses', []),
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = DB::transaction(function () use ($request) {
            $area = SupportArea::query()
                ->active()
                ->findOrFail($request->integer('area_id'));

            $ticket = Ticket::create([
                'requester_id' => $request->user()->id,
                'subject' => $request->string('subject'),
                'description' => $request->string('description'),
                'area_id' => $area->id,
                'current_area' => $area->slug,
                'status' => Ticket::STATUS_OPEN,
            ]);

            $this->recordEvent(
                ticket: $ticket,
                actorId: $request->user()->id,
                type: 'created',
                note: $request->string('description'),
                toStatus: Ticket::STATUS_OPEN,
                toAreaId: $ticket->area_id,
                toArea: $area->slug
            );

            return $ticket;
        });

        return redirect()
            ->route('support.tickets.show', $ticket)
            ->with('status', __('Ticket aberto com sucesso.'));
    }

    public function show(Request $request, Ticket $ticket): View
    {
        abort_unless($ticket->isVisibleTo($request->user()), 403);

        $ticket->load(['requester', 'assignedTo', 'area', 'events.actor', 'events.fromArea', 'events.toArea']);

        return view('support.tickets.show', [
            'ticket' => $ticket,
            'areas' => SupportArea::query()->active()->orderBy('name')->get(),
            'statuses' => config('support.statuses', []),
            'isTechnical' => $request->user()->isTechnical(),
            'isAssignedToCurrentUser' => $ticket->assigned_to_id === $request->user()->id,
            'canHandleCurrentArea' => $request->user()->canWorkSupportArea($ticket->area),
            'isRequester' => $ticket->requester_id === $request->user()->id,
        ]);
    }

    public function comment(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($ticket->isVisibleTo($request->user()), 403);

        $data = $request->validate([
            'note' => ['required', 'string', 'min:3'],
        ]);

        $this->recordEvent(
            ticket: $ticket,
            actorId: $request->user()->id,
            type: 'comment',
            note: $data['note']
        );

        return back()->with('status', __('Informação adicionada ao ticket.'));
    }

    public function take(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureTechnical($request, $ticket);
        $this->ensureAreaAccess($request->user(), $ticket->area);
        abort_unless($ticket->assigned_to_id === null, 403);
        $this->ensureTicketStatus($ticket, [
            Ticket::STATUS_OPEN,
            Ticket::STATUS_PENDING,
        ], __('Este ticket não pode ser assumido neste estado.'));

        $fromStatus = $ticket->status;

        $this->recordEvent(
            ticket: $ticket,
            actorId: $request->user()->id,
            type: 'assigned',
            note: $request->string('note') ?: __('Ticket assumido e movido para análise.'),
            fromStatus: $fromStatus,
            toStatus: Ticket::STATUS_ANALYSIS
        );

        $ticket->forceFill([
            'assigned_to_id' => $request->user()->id,
            'status' => Ticket::STATUS_ANALYSIS,
        ])->save();

        return back()->with('status', __('Ticket assumido com sucesso.'));
    }

    public function transfer(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureTechnical($request, $ticket);
        $this->ensureAreaAccess($request->user(), $ticket->area);
        $this->ensureTicketStatus($ticket, [
            Ticket::STATUS_OPEN,
            Ticket::STATUS_ANALYSIS,
            Ticket::STATUS_PENDING,
        ], __('Este ticket não pode ser encaminhado neste estado.'));

        $data = $request->validate([
            'target_area_id' => [
                'required',
                'integer',
                Rule::exists('support_areas', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'note' => ['required', 'string', 'min:3'],
        ]);

        $previousArea = $ticket->area;
        $previousStatus = $ticket->status;
        $targetArea = SupportArea::query()->findOrFail($data['target_area_id']);

        $ticket->forceFill([
            'area_id' => $targetArea->id,
            'current_area' => $targetArea->slug,
            'assigned_to_id' => null,
            'status' => Ticket::STATUS_PENDING,
        ])->save();

        $this->recordEvent(
            ticket: $ticket,
            actorId: $request->user()->id,
            type: 'transferred',
            note: $data['note'],
            fromStatus: $previousStatus,
            toStatus: Ticket::STATUS_PENDING,
            fromAreaId: $previousArea?->id,
            toAreaId: $targetArea->id,
            fromArea: $previousArea?->slug,
            toArea: $targetArea->slug
        );

        return back()->with('status', __('Ticket encaminhado para outra área.'));
    }

    public function requestInfo(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureTechnical($request, $ticket);
        $this->ensureAreaAccess($request->user(), $ticket->area);
        abort_unless($ticket->assigned_to_id === $request->user()->id, 403);
        $this->ensureTicketStatus($ticket, [
            Ticket::STATUS_ANALYSIS,
        ], __('Este ticket não pode solicitar informações neste estado.'));

        $data = $request->validate([
            'note' => ['required', 'string', 'min:3'],
        ]);

        $fromStatus = $ticket->status;

        $ticket->forceFill([
            'status' => Ticket::STATUS_PENDING,
        ])->save();

        $this->recordEvent(
            ticket: $ticket,
            actorId: $request->user()->id,
            type: 'requested_info',
            note: $data['note'],
            fromStatus: $fromStatus,
            toStatus: Ticket::STATUS_PENDING
        );

        return back()->with('status', __('Ticket devolvido ao solicitante para complementação.'));
    }

    public function resolve(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureTechnical($request, $ticket);
        $this->ensureTicketStatus($ticket, [
            Ticket::STATUS_OPEN,
            Ticket::STATUS_ANALYSIS,
            Ticket::STATUS_PENDING,
        ], __('Este ticket não pode ser solucionado neste estado.'));

        $data = $request->validate([
            'note' => ['required', 'string', 'min:3'],
        ]);

        $fromStatus = $ticket->status;

        $ticket->forceFill([
            'status' => Ticket::STATUS_RESOLVED,
            'resolved_at' => now(),
        ])->save();

        $this->recordEvent(
            ticket: $ticket,
            actorId: $request->user()->id,
            type: 'resolved',
            note: $data['note'],
            fromStatus: $fromStatus,
            toStatus: Ticket::STATUS_RESOLVED
        );

        return back()->with('status', __('Ticket marcado como solucionado.'));
    }

    public function returnToSupport(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureRequester($request, $ticket);
        $this->ensureTicketStatus($ticket, [
            Ticket::STATUS_PENDING,
            Ticket::STATUS_RESOLVED,
        ], __('Somente tickets finalizados ou aguardando informações podem ser devolvidos para a TI.'));
        abort_unless($ticket->assigned_to_id !== null, 403);

        $data = $request->validate([
            'note' => ['required', 'string', 'min:3'],
        ]);

        $fromStatus = $ticket->status;

        $ticket->forceFill([
            'status' => Ticket::STATUS_ANALYSIS,
            'resolved_at' => null,
        ])->save();

        $this->recordEvent(
            ticket: $ticket,
            actorId: $request->user()->id,
            type: 'pending',
            note: $data['note'],
            fromStatus: $fromStatus,
            toStatus: Ticket::STATUS_ANALYSIS
        );

        return back()->with('status', __('Ticket devolvido para a TI.'));
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureRequester($request, $ticket);
        $this->ensureTicketStatus($ticket, [
            Ticket::STATUS_RESOLVED,
        ], __('Somente tickets solucionados podem ser finalizados.'));

        $data = $request->validate([
            'note' => ['nullable', 'string', 'min:3'],
        ]);

        $fromStatus = $ticket->status;

        $ticket->forceFill([
            'status' => Ticket::STATUS_CLOSED,
            'closed_at' => now(),
        ])->save();

        $this->recordEvent(
            ticket: $ticket,
            actorId: $request->user()->id,
            type: 'closed',
            note: $data['note'] ?? __('Usuário confirmou a solução do ticket.'),
            fromStatus: $fromStatus,
            toStatus: Ticket::STATUS_CLOSED
        );

        return back()->with('status', __('Ticket finalizado.'));
    }

    private function ensureTechnical(Request $request, Ticket $ticket): void
    {
        abort_unless($request->user()->isTechnical(), 403);
        abort_unless($ticket->isVisibleTo($request->user()), 403);
    }

    private function ensureRequester(Request $request, Ticket $ticket): void
    {
        abort_unless($ticket->requester_id === $request->user()->id, 403);
    }

    private function ensureAreaAccess(User $user, ?SupportArea $area): void
    {
        abort_unless($area && $user->canWorkSupportArea($area), 403);
    }

    private function ensureTicketStatus(Ticket $ticket, array $allowedStatuses, string $message): void
    {
        if (! in_array($ticket->status, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'status' => $message,
            ]);
        }
    }

    private function recordEvent(
        Ticket $ticket,
        ?int $actorId,
        string $type,
        ?string $note = null,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        ?string $fromArea = null,
        ?string $toArea = null,
        ?int $fromAreaId = null,
        ?int $toAreaId = null,
    ): TicketEvent {
        $event = $ticket->events()->create([
            'actor_id' => $actorId,
            'type' => $type,
            'note' => $note,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'from_area' => $fromArea,
            'to_area' => $toArea,
            'from_area_id' => $fromAreaId,
            'to_area_id' => $toAreaId,
        ]);

        $ticket->touch();

        return $event;
    }
}
