<?php

namespace App\Livewire\Support;

use App\Models\SupportArea;
use App\Models\SystemSetting;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Component;

class TicketQueue extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $area = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingArea(): void
    {
        $this->resetPage();
    }

    public function getRefreshIntervalProperty(): int
    {
        return max(1, min(300, SystemSetting::integer('queue_refresh_interval_seconds', 15)));
    }

    public function render(): View
    {
        $user = Auth::user();

        $tickets = Ticket::query()
            ->with(['requester', 'assignedTo', 'area'])
            ->visibleTo($user)
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->area !== '', fn ($query) => $query->where('area_id', $this->area))
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($query) use ($search) {
                    $query->where('subject', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12);

        return view('livewire.support.ticket-queue', [
            'tickets' => $tickets,
            'areas' => SupportArea::query()->active()->orderBy('name')->get(),
            'statuses' => config('support.statuses', []),
            'isTechnical' => $user->isTechnical(),
        ]);
    }
}
