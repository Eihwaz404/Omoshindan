<?php

namespace Tests\Feature\Support;

use App\Models\SupportArea;
use App\Models\SupportSubject;
use App\Models\SystemSetting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketSlaCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_show_page_polls_sla_card_using_dashboard_interval(): void
    {
        SystemSetting::put('dashboard_refresh_interval_seconds', 12);

        $requester = User::factory()->create();
        $area = SupportArea::query()->where('slug', 'service_desk')->firstOrFail();
        $subject = SupportSubject::create([
            'category' => 1,
            'name' => 'SLA polling',
            'is_active' => true,
        ]);

        $ticket = Ticket::create([
            'requester_id' => $requester->id,
            'subject_id' => $subject->id,
            'subject' => $subject->name,
            'description' => 'Ticket para validar o polling do SLA.',
            'area_id' => $area->id,
            'current_area' => $area->slug,
            'status' => Ticket::STATUS_OPEN,
            'priority' => Ticket::PRIORITY_NORMAL,
        ]);

        $this->actingAs($requester)
            ->get(route('support.tickets.show', $ticket))
            ->assertOk()
            ->assertSee('wire:poll.12s', false)
            ->assertSee('Tempo restante para concluir o chamado')
            ->assertSee('min restantes');
    }
}
