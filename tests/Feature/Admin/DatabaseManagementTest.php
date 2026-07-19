<?php

namespace Tests\Feature\Admin;

use App\Models\SupportArea;
use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_database_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.database.index'));

        $response->assertOk();
        $response->assertSee('Banco de dados');
        $response->assertSee('Tickets');
    }

    public function test_super_admin_can_sanitize_tickets_table_and_reset_ids(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $area = SupportArea::firstOrCreate([
            'slug' => 'service_desk',
        ], [
            'name' => 'Service Desk',
            'description' => 'Triagem inicial',
            'is_active' => true,
        ]);

        $requester = User::factory()->create();

        $ticket = Ticket::create([
            'requester_id' => $requester->id,
            'subject' => 'Chamado de teste',
            'description' => 'Descrição de teste',
            'area_id' => $area->id,
            'current_area' => $area->slug,
            'status' => Ticket::STATUS_OPEN,
        ]);

        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'actor_id' => $admin->id,
            'type' => 'comment',
            'note' => 'Evento de teste',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.database.sanitize'), [
            'table' => 'tickets',
        ]);

        $response->assertRedirect(route('admin.database.index'));
        $response->assertSessionHas('status', __('Tabela Tickets sanitizada com sucesso.'));

        $this->assertDatabaseCount('tickets', 0);
        $this->assertDatabaseCount('ticket_events', 0);

        $newTicket = Ticket::create([
            'requester_id' => $requester->id,
            'subject' => 'Novo chamado',
            'description' => 'Novo registro após limpeza',
            'area_id' => $area->id,
            'current_area' => $area->slug,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $this->assertSame(1, $newTicket->id);
    }

    public function test_non_privileged_user_cannot_access_database_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'support',
        ]);

        $response = $this->actingAs($user)->get(route('admin.database.index'));

        $response->assertForbidden();
    }
}
