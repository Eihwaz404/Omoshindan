<?php

namespace Tests\Feature\Support;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_technical_user_cannot_take_resolved_ticket(): void
    {
        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
            'permissions' => ['support.areas.service_desk'],
        ]);

        $ticket = $this->createTicket($requester, [
            'status' => Ticket::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);

        $response = $this->actingAs($support)
            ->from(route('support.tickets.show', $ticket, false))
            ->post(route('support.tickets.take', $ticket), [
                'note' => 'Tentativa de assumir um ticket já resolvido.',
            ]);

        $response->assertRedirect(route('support.tickets.show', $ticket, false));
        $response->assertSessionHasErrors('status');

        $ticket->refresh();

        $this->assertSame(Ticket::STATUS_RESOLVED, $ticket->status);
        $this->assertNull($ticket->assigned_to_id);
    }

    public function test_requester_can_return_resolved_ticket_and_clear_resolution_timestamp(): void
    {
        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
            'permissions' => ['support.areas.service_desk'],
        ]);

        $ticket = $this->createTicket($requester, [
            'assigned_to_id' => $support->id,
            'status' => Ticket::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);

        $response = $this->actingAs($requester)
            ->from(route('support.tickets.show', $ticket, false))
            ->post(route('support.tickets.return', $ticket), [
                'note' => 'Ainda preciso de uma correção.',
            ]);

        $response->assertRedirect(route('support.tickets.show', $ticket, false));
        $response->assertSessionHas('status', __('Ticket devolvido para a TI.'));

        $ticket->refresh();

        $this->assertSame(Ticket::STATUS_PENDING, $ticket->status);
        $this->assertNull($ticket->assigned_to_id);
        $this->assertNull($ticket->resolved_at);
    }

    public function test_ticket_is_auto_routed_to_detected_area_when_opened(): void
    {
        $requester = User::factory()->create();

        $response = $this->actingAs($requester)->post(route('support.tickets.store'), [
            'subject' => 'Servidor sem acesso à VPN',
            'description' => 'O servidor e a VPN da unidade caíram após a manutenção da rede.',
        ]);

        $response->assertRedirect();

        $ticket = Ticket::query()->latest('id')->firstOrFail();

        $this->assertSame('infrastructure', $ticket->current_area);
        $this->assertSame(Ticket::STATUS_OPEN, $ticket->status);
    }

    public function test_technical_user_without_area_permission_cannot_take_other_area_ticket(): void
    {
        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
            'permissions' => ['support.areas.service_desk'],
        ]);

        $ticket = $this->createTicket($requester, [
            'current_area' => 'infrastructure',
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($support)
            ->post(route('support.tickets.take', $ticket), [
                'note' => 'Tentativa fora da área permitida.',
            ]);

        $response->assertForbidden();
    }

    private function createTicket(User $requester, array $attributes = []): Ticket
    {
        return Ticket::create(array_merge([
            'requester_id' => $requester->id,
            'subject' => 'Impressora com erro',
            'description' => 'A impressora da recepção parou de responder após a atualização do sistema.',
            'current_area' => 'service_desk',
            'status' => Ticket::STATUS_OPEN,
        ], $attributes));
    }
}
