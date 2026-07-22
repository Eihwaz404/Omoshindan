<?php

namespace App\Support;

use App\Models\SystemSetting;
use App\Models\Ticket;
use App\Models\TicketEvent;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TicketSlaCalculator
{
    /**
     * Ticket statuses that count toward the SLA clock.
     */
    private const ACTIVE_STATUSES = [
        Ticket::STATUS_OPEN,
        Ticket::STATUS_ANALYSIS,
    ];

    /**
     * Build a summary for display.
     *
     * @return array{
     *     priority_minutes: int,
     *     elapsed_minutes: int,
     *     remaining_minutes: int,
 *     progress_percent: int,
 *     display_text: string,
 *     tone: string,
 *     tone_color: string,
 *     is_paused: bool,
 *     is_overdue: bool,
 *     is_complete: bool
 * }
     */
    public function summary(Ticket $ticket, ?CarbonInterface $asOf = null): array
    {
        $asOfMoment = $this->moment($asOf ?? now());
        $priorityMinutes = $this->priorityMinutes($ticket);
        $elapsedMinutes = $this->elapsedBusinessMinutes($ticket, $asOfMoment);
        $isComplete = in_array($ticket->status, [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED], true);
        $isPaused = $ticket->status === Ticket::STATUS_PENDING;
        $remainingMinutes = max(0, $priorityMinutes - $elapsedMinutes);
        $overdueMinutes = max(0, $elapsedMinutes - $priorityMinutes);
        $isOverdue = ! $isComplete && ! $isPaused && $remainingMinutes === 0 && $elapsedMinutes >= $priorityMinutes;
        $progressPercent = $priorityMinutes > 0
            ? min(100, (int) round(($elapsedMinutes / $priorityMinutes) * 100))
            : 100;

        return [
            'priority_minutes' => $priorityMinutes,
            'elapsed_minutes' => $elapsedMinutes,
            'remaining_minutes' => $remainingMinutes,
            'progress_percent' => $progressPercent,
            'display_text' => $this->displayText($remainingMinutes, $overdueMinutes, $isPaused, $isComplete, $isOverdue),
            'tone' => $this->tone($progressPercent, $isPaused, $isOverdue, $isComplete),
            'tone_color' => $this->toneColor($progressPercent, $isPaused, $isOverdue, $isComplete),
            'is_paused' => $isPaused,
            'is_overdue' => $isOverdue,
            'is_complete' => $isComplete,
        ];
    }

    public function priorityMinutes(Ticket $ticket): int
    {
        $priority = $ticket->priority ?: Ticket::PRIORITY_NORMAL;

        $default = match ($priority) {
            Ticket::PRIORITY_LOW => 1440,
            Ticket::PRIORITY_NORMAL => 720,
            Ticket::PRIORITY_MEDIUM => 240,
            Ticket::PRIORITY_HIGH => 60,
            default => 720,
        };

        return max(1, SystemSetting::integer("priority_{$priority}_resolution_minutes", $default));
    }

    public function elapsedBusinessMinutes(Ticket $ticket, ?CarbonInterface $asOf = null): int
    {
        $asOfMoment = $this->moment($asOf ?? now());
        $startMoment = $this->moment($ticket->created_at ?? $asOfMoment);
        $endMoment = $this->terminalMoment($ticket, $asOfMoment);

        if ($endMoment->lessThanOrEqualTo($startMoment)) {
            return 0;
        }

        $events = $this->orderedEvents($ticket);
        $cursor = $startMoment;
        $currentStatus = $events->first()?->to_status ?? $ticket->status;
        $elapsedMinutes = 0;

        foreach ($events->slice(1) as $event) {
            $transitionAt = $this->moment($event->created_at ?? $cursor);

            if ($this->countsTowardSla($currentStatus)) {
                $elapsedMinutes += $this->businessMinutesBetween($cursor, $transitionAt);
            }

            $cursor = $transitionAt;
            $currentStatus = $event->to_status ?? $currentStatus;
        }

        if ($cursor->lessThan($endMoment) && $this->countsTowardSla($currentStatus)) {
            $elapsedMinutes += $this->businessMinutesBetween($cursor, $endMoment);
        }

        return $elapsedMinutes;
    }

    /**
     * @return Collection<int, \App\Models\TicketEvent>
     */
    private function orderedEvents(Ticket $ticket): Collection
    {
        return TicketEvent::query()
            ->where('ticket_id', $ticket->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    private function terminalMoment(Ticket $ticket, CarbonImmutable $asOfMoment): CarbonImmutable
    {
        if ($ticket->closed_at) {
            return $this->moment($ticket->closed_at);
        }

        if ($ticket->resolved_at) {
            return $this->moment($ticket->resolved_at);
        }

        return $asOfMoment;
    }

    private function countsTowardSla(?string $status): bool
    {
        return in_array($status, self::ACTIVE_STATUSES, true);
    }

    private function businessMinutesBetween(CarbonImmutable $start, CarbonImmutable $end): int
    {
        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        $minutes = 0;
        $currentDay = $start->startOfDay();
        $lastDay = $end->startOfDay();

        while ($currentDay->lessThanOrEqualTo($lastDay)) {
            if (! in_array($currentDay->dayOfWeekIso, [1, 2, 3, 4, 5], true)) {
                $currentDay = $currentDay->addDay();

                continue;
            }

            foreach ($this->workingIntervalsForDay($currentDay) as [$intervalStart, $intervalEnd]) {
                $clampedStart = $intervalStart->max($start);
                $clampedEnd = $intervalEnd->min($end);

                if ($clampedEnd->greaterThan($clampedStart)) {
                    $minutes += $clampedStart->diffInMinutes($clampedEnd);
                }
            }

            $currentDay = $currentDay->addDay();
        }

        return $minutes;
    }

    /**
     * @return array<int, array{0: CarbonImmutable, 1: CarbonImmutable}>
     */
    private function workingIntervalsForDay(CarbonImmutable $day): array
    {
        $dayKey = match ($day->dayOfWeekIso) {
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            default => null,
        };

        if ($dayKey === null) {
            return [];
        }

        $workStart = $this->timeOnDay($day, SystemSetting::getValue("work_schedule_{$dayKey}_start", '08:00'));
        $workEnd = $this->timeOnDay($day, SystemSetting::getValue("work_schedule_{$dayKey}_end", '17:00'));
        $lunchStart = $this->timeOnDay($day, SystemSetting::getValue("work_schedule_{$dayKey}_lunch_start", '12:00'));
        $lunchEnd = $this->timeOnDay($day, SystemSetting::getValue("work_schedule_{$dayKey}_lunch_end", '13:00'));

        if ($workEnd->lessThanOrEqualTo($workStart)) {
            return [];
        }

        $intervals = [];

        if ($lunchStart->greaterThan($workStart)) {
            $intervals[] = [$workStart, $lunchStart->min($workEnd)];
        }

        if ($lunchEnd->lessThan($workEnd)) {
            $intervals[] = [$lunchEnd->max($workStart), $workEnd];
        }

        if ($intervals === []) {
            $intervals[] = [$workStart, $workEnd];
        }

        return array_values(array_filter($intervals, fn (array $interval) => $interval[1]->greaterThan($interval[0])));
    }

    private function timeOnDay(CarbonImmutable $day, string $time): CarbonImmutable
    {
        return $day->setTimeFromTimeString($time);
    }

    private function moment(CarbonInterface $moment): CarbonImmutable
    {
        return CarbonImmutable::instance($moment);
    }

    private function displayText(int $remainingMinutes, int $overdueMinutes, bool $isPaused, bool $isComplete, bool $isOverdue): string
    {
        if ($isComplete) {
            return 'Concluído';
        }

        if ($isPaused) {
            return 'Contagem pausada';
        }

        if ($isOverdue) {
            return 'Prazo excedido em '.$this->formatMinutes($overdueMinutes);
        }

        return 'Restam '.$this->formatMinutes($remainingMinutes).' para solução';
    }

    private function tone(int $progressPercent, bool $isPaused, bool $isOverdue, bool $isComplete): string
    {
        if ($isComplete) {
            return 'bg-emerald-500';
        }

        if ($isPaused) {
            return 'bg-slate-500';
        }

        if ($isOverdue) {
            return 'bg-rose-500';
        }

        return match (true) {
            $progressPercent >= 90 => 'bg-rose-500',
            $progressPercent >= 75 => 'bg-orange-500',
            $progressPercent >= 50 => 'bg-amber-400',
            default => 'bg-emerald-500',
        };
    }

    private function toneColor(int $progressPercent, bool $isPaused, bool $isOverdue, bool $isComplete): string
    {
        if ($isComplete) {
            return '#10b981';
        }

        if ($isPaused) {
            return '#64748b';
        }

        if ($isOverdue) {
            return '#ef4444';
        }

        return match (true) {
            $progressPercent >= 90 => '#ef4444',
            $progressPercent >= 75 => '#f97316',
            $progressPercent >= 50 => '#f59e0b',
            default => '#22c55e',
        };
    }

    private function formatMinutes(int $minutes): string
    {
        $minutes = max(0, $minutes);
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours === 0) {
            return "{$remainingMinutes}m";
        }

        if ($remainingMinutes === 0) {
            return "{$hours}h";
        }

        return "{$hours}h {$remainingMinutes}m";
    }
}
