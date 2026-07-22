<?php

namespace Tests\Feature\Support;

use App\Models\SupportArea;
use App\Models\SupportSubject;
use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Models\User;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketSlaTest extends TestCase
{
    use RefreshDatabase;

    public function test_sla_counts_only_business_hours_and_skips_lunch_break(): void
    {
        $this->seedWorkSettings();
        SystemSetting::put('priority_medium_resolution_minutes', 240);

        $requester = User::factory()->create();
        $area = SupportArea::query()->where('slug', 'service_desk')->firstOrFail();
        $subject = SupportSubject::create([
            'category' => 1,
            'name' => 'Teste SLA',
            'is_active' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-07-20 09:00:00'));
        $ticket = Ticket::create([
            'requester_id' => $requester->id,
            'subject_id' => $subject->id,
            'subject' => $subject->name,
            'description' => 'Teste de SLA com horas úteis.',
            'area_id' => $area->id,
            'current_area' => $area->slug,
            'status' => Ticket::STATUS_ANALYSIS,
            'priority' => Ticket::PRIORITY_MEDIUM,
        ]);

        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'type' => 'created',
            'to_status' => Ticket::STATUS_OPEN,
            'created_at' => Carbon::parse('2026-07-20 09:00:00'),
            'updated_at' => Carbon::parse('2026-07-20 09:00:00'),
        ]);

        Carbon::setTestNow(Carbon::parse('2026-07-20 14:00:00'));

        $sla = $ticket->fresh(['events'])->slaSummary();

        $this->assertSame(240, $sla['elapsed_minutes']);
        $this->assertSame(0, $sla['remaining_minutes']);
        $this->assertTrue($sla['is_overdue']);
        $this->assertStringContainsString('Prazo excedido', $sla['display_text']);

        Carbon::setTestNow();
    }

    public function test_sla_pauses_when_ticket_is_pending(): void
    {
        $this->seedWorkSettings();
        SystemSetting::put('priority_medium_resolution_minutes', 240);

        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
        ]);
        $area = SupportArea::query()->where('slug', 'service_desk')->firstOrFail();
        $subject = SupportSubject::create([
            'category' => 1,
            'name' => 'Teste SLA pendente',
            'is_active' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-07-20 09:00:00'));
        $ticket = Ticket::create([
            'requester_id' => $requester->id,
            'subject_id' => $subject->id,
            'subject' => $subject->name,
            'description' => 'Teste de pausa de SLA.',
            'area_id' => $area->id,
            'current_area' => $area->slug,
            'status' => Ticket::STATUS_PENDING,
            'priority' => Ticket::PRIORITY_MEDIUM,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-07-20 09:00:00'));
        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'actor_id' => $support->id,
            'type' => 'created',
            'to_status' => Ticket::STATUS_OPEN,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-07-20 10:00:00'));
        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'actor_id' => $support->id,
            'type' => 'analysis',
            'from_status' => Ticket::STATUS_OPEN,
            'to_status' => Ticket::STATUS_ANALYSIS,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-07-20 11:00:00'));
        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'actor_id' => $support->id,
            'type' => 'requested_info',
            'from_status' => Ticket::STATUS_ANALYSIS,
            'to_status' => Ticket::STATUS_PENDING,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-07-20 15:00:00'));

        $sla = $ticket->fresh(['events'])->slaSummary();

        $this->assertSame(120, $sla['elapsed_minutes']);
        $this->assertSame(120, $sla['remaining_minutes']);
        $this->assertTrue($sla['is_paused']);
        $this->assertSame('Contagem pausada', $sla['display_text']);

        Carbon::setTestNow();
    }

    private function seedWorkSettings(): void
    {
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $dayKey) {
            SystemSetting::put("work_schedule_{$dayKey}_start", '08:00');
            SystemSetting::put("work_schedule_{$dayKey}_end", '17:00');
            SystemSetting::put("work_schedule_{$dayKey}_lunch_start", '12:00');
            SystemSetting::put("work_schedule_{$dayKey}_lunch_end", '13:00');
        }
    }
}
