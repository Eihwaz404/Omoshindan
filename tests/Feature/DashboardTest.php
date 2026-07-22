<?php

namespace Tests\Feature;

use App\Models\SupportArea;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_support_indicators(): void
    {
        $requester = User::factory()->create();
        $technicalA = User::factory()->create([
            'name' => 'Carlos TI',
            'role' => 'support',
        ]);
        $technicalB = User::factory()->create([
            'name' => 'Marina TI',
            'role' => 'admin',
        ]);

        $serviceDesk = SupportArea::query()->where('slug', 'service_desk')->firstOrFail();
        $infrastructure = SupportArea::query()->where('slug', 'infrastructure')->firstOrFail();

        Ticket::create([
            'requester_id' => $requester->id,
            'assigned_to_id' => $technicalA->id,
            'subject' => 'Impressora com erro',
            'description' => 'Ticket em aberto.',
            'area_id' => $serviceDesk->id,
            'current_area' => $serviceDesk->slug,
            'status' => Ticket::STATUS_OPEN,
        ]);

        Ticket::create([
            'requester_id' => $requester->id,
            'assigned_to_id' => $technicalA->id,
            'subject' => 'Sistema lento',
            'description' => 'Ticket em análise.',
            'area_id' => $infrastructure->id,
            'current_area' => $infrastructure->slug,
            'status' => Ticket::STATUS_ANALYSIS,
        ]);

        Ticket::create([
            'requester_id' => $requester->id,
            'assigned_to_id' => $technicalB->id,
            'subject' => 'Acesso pendente',
            'description' => 'Ticket pendente.',
            'area_id' => $infrastructure->id,
            'current_area' => $infrastructure->slug,
            'status' => Ticket::STATUS_PENDING,
        ]);

        $response = $this->actingAs($requester)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('wire:poll.30s', false);
        $response->assertSee('Indicadores em tempo real do suporte.');
        $response->assertSee('Chamados por status');
        $response->assertSeeInOrder(['Total de chamados', '3']);
        $response->assertSeeInOrder(['Aberto', '1']);
        $response->assertSeeInOrder(['Em Análise', '1']);
        $response->assertSeeInOrder(['Pendente', '1']);
        $response->assertSeeInOrder([$serviceDesk->name, '1']);
        $response->assertSeeInOrder([$infrastructure->name, '2']);
        $response->assertSeeInOrder([$technicalA->name, '2']);
        $response->assertSeeInOrder([$technicalB->name, '1']);
    }
}
