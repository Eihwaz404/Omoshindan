<?php

namespace App\Livewire\Support;

use App\Models\SystemSetting;
use App\Models\Ticket;
use App\Support\TicketSlaCalculator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TicketSlaCard extends Component
{
    public int $ticketId;

    public function mount(Ticket $ticket): void
    {
        $this->ticketId = $ticket->id;
    }

    public function getRefreshIntervalProperty(): int
    {
        return max(1, min(300, SystemSetting::integer('dashboard_refresh_interval_seconds', 30)));
    }

    public function render(): View
    {
        $ticket = Ticket::query()
            ->with(['requester', 'assignedTo', 'area'])
            ->findOrFail($this->ticketId);

        abort_unless($ticket->isVisibleTo(Auth::user()), 403);

        $ticket->setAttribute('sla', app(TicketSlaCalculator::class)->summary($ticket));

        return view('livewire.support.ticket-sla-card', [
            'ticket' => $ticket,
            'sla' => $ticket->getAttribute('sla'),
        ]);
    }
}
