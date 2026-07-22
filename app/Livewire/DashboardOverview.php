<?php

namespace App\Livewire;

use App\Models\SupportArea;
use App\Models\SystemSetting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardOverview extends Component
{
    public function getRefreshIntervalProperty(): int
    {
        return max(1, min(300, SystemSetting::integer('dashboard_refresh_interval_seconds', 30)));
    }

    public function render(): View
    {
        $statusLabels = collect(config('support.statuses', []));
        $statusCounts = Ticket::query()
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statusCards = $statusLabels->map(function (string $label, string $status) use ($statusCounts) {
            return [
                'status' => $status,
                'label' => $label,
                'count' => (int) ($statusCounts[$status] ?? 0),
            ];
        })->values();

        $areaCards = SupportArea::query()
            ->withCount('tickets')
            ->orderByDesc('tickets_count')
            ->orderBy('name')
            ->get();

        $technicalUsers = User::query()
            ->whereIn('role', ['super_admin', 'admin', 'support'])
            ->withCount('assignedTickets')
            ->orderByDesc('assigned_tickets_count')
            ->orderBy('name')
            ->get();

        return view('livewire.dashboard-overview', [
            'totalTickets' => Ticket::query()->count(),
            'statusCards' => $statusCards,
            'statusMax' => max($statusCards->pluck('count')->all() ?: [0]),
            'areaCards' => $areaCards,
            'areaMax' => max($areaCards->pluck('tickets_count')->all() ?: [0]),
            'technicalUsers' => $technicalUsers,
            'technicalMax' => max($technicalUsers->pluck('assigned_tickets_count')->all() ?: [0]),
        ]);
    }
}
