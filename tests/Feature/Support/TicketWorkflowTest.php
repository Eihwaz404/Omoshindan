<?php

namespace Tests\Feature\Support;

use App\Models\SupportArea;
use App\Models\SupportSubject;
use App\Models\TicketAttachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_technical_user_cannot_take_resolved_ticket(): void
    {
        $area = $this->createArea('service_desk', 'Service Desk');
        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
        ]);
        $area->users()->attach($support);

        $ticket = $this->createTicket($requester, [
            'area_id' => $area->id,
            'current_area' => $area->slug,
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

    public function test_requester_can_return_ticket_to_the_same_technical_user(): void
    {
        $area = $this->createArea('service_desk', 'Service Desk');
        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
        ]);
        $area->users()->attach($support);

        $ticket = $this->createTicket($requester, [
            'area_id' => $area->id,
            'current_area' => $area->slug,
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

        $this->assertSame(Ticket::STATUS_ANALYSIS, $ticket->status);
        $this->assertSame($support->id, $ticket->assigned_to_id);
        $this->assertNull($ticket->resolved_at);
    }

    public function test_ticket_is_created_with_user_selected_area(): void
    {
        $this->createArea('service_desk', 'Service Desk');
        $infrastructure = $this->createArea('infrastructure', 'Infraestrutura');
        $this->createArea('systems', 'Sistemas');
        $this->createArea('development', 'Desenvolvimento');
        $subject = $this->createSubject(2, 'Sem conexão com a VPN');

        $requester = User::factory()->create();

        $response = $this->actingAs($requester)->post(route('support.tickets.store'), [
            'description' => 'O servidor e a VPN da unidade caíram após a manutenção da rede.',
            'area_id' => $infrastructure->id,
            'subject_id' => $subject->id,
        ]);

        $response->assertRedirect();

        $ticket = Ticket::query()->latest('id')->firstOrFail();

        $this->assertSame($infrastructure->id, $ticket->area_id);
        $this->assertSame($infrastructure->slug, $ticket->current_area);
        $this->assertSame(Ticket::STATUS_OPEN, $ticket->status);
    }

    public function test_ticket_can_be_created_with_jpg_attachments(): void
    {
        Storage::fake('public');

        $area = $this->createArea('service_desk', 'Service Desk');
        $subject = $this->createSubject(1, 'Acesso ao sistema');
        $requester = User::factory()->create();

        $response = $this->actingAs($requester)->post(route('support.tickets.store'), [
            'subject_id' => $subject->id,
            'description' => 'A aplicação está apresentando falha ao abrir a tela de login e preciso registrar um teste com imagem.',
            'area_id' => $area->id,
            'images' => [
                UploadedFile::fake()->image('evidencia.jpg')->size(1200),
            ],
        ]);

        $response->assertRedirect();

        $attachment = TicketAttachment::query()->firstOrFail();

        $this->assertDatabaseHas('ticket_attachments', [
            'ticket_id' => $attachment->ticket_id,
            'ticket_event_id' => $attachment->ticket_event_id,
            'original_name' => 'evidencia.jpg',
        ]);

        Storage::disk('public')->assertExists($attachment->path);
    }

    public function test_technical_user_without_area_permission_cannot_take_other_area_ticket(): void
    {
        $serviceDesk = $this->createArea('service_desk', 'Service Desk');
        $infrastructure = $this->createArea('infrastructure', 'Infraestrutura');

        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
        ]);
        $serviceDesk->users()->attach($support);

        $ticket = $this->createTicket($requester, [
            'area_id' => $infrastructure->id,
            'current_area' => $infrastructure->slug,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($support)
            ->post(route('support.tickets.take', $ticket), [
            'note' => 'Tentativa fora da área permitida.',
            ]);

        $response->assertForbidden();
    }

    public function test_technical_user_can_assume_ticket_and_move_it_to_analysis(): void
    {
        $area = $this->createArea('service_desk', 'Service Desk');
        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
        ]);
        $area->users()->attach($support);

        $ticket = $this->createTicket($requester, [
            'area_id' => $area->id,
            'current_area' => $area->slug,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $this->actingAs($support)->post(route('support.tickets.take', $ticket), [
            'note' => 'Assumindo o ticket.',
        ])->assertRedirect();

        $ticket->refresh();

        $this->assertSame(Ticket::STATUS_ANALYSIS, $ticket->status);
        $this->assertSame($support->id, $ticket->assigned_to_id);
    }

    public function test_technical_user_can_request_information_and_get_ticket_back_from_requester(): void
    {
        $area = $this->createArea('service_desk', 'Service Desk');
        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
        ]);
        $area->users()->attach($support);

        $ticket = $this->createTicket($requester, [
            'area_id' => $area->id,
            'current_area' => $area->slug,
            'assigned_to_id' => $support->id,
            'status' => Ticket::STATUS_ANALYSIS,
        ]);

        $this->actingAs($support)
            ->from(route('support.tickets.show', $ticket, false))
            ->post(route('support.tickets.request-info', $ticket), [
                'note' => 'Preciso do número de série do equipamento.',
            ])
            ->assertRedirect(route('support.tickets.show', $ticket, false));

        $ticket->refresh();

        $this->assertSame(Ticket::STATUS_PENDING, $ticket->status);
        $this->assertSame($support->id, $ticket->assigned_to_id);

        $this->actingAs($requester)
            ->from(route('support.tickets.show', $ticket, false))
            ->post(route('support.tickets.return', $ticket), [
                'note' => 'Segue o número de série solicitado.',
            ])
            ->assertRedirect(route('support.tickets.show', $ticket, false));

        $ticket->refresh();

        $this->assertSame(Ticket::STATUS_ANALYSIS, $ticket->status);
        $this->assertSame($support->id, $ticket->assigned_to_id);
    }

    public function test_transfer_clears_assignee_and_returns_ticket_to_pending_queue(): void
    {
        $serviceDesk = $this->createArea('service_desk', 'Service Desk');
        $infrastructure = $this->createArea('infrastructure', 'Infraestrutura');

        $requester = User::factory()->create();
        $support = User::factory()->create([
            'role' => 'support',
        ]);
        $serviceDesk->users()->attach($support);

        $ticket = $this->createTicket($requester, [
            'area_id' => $serviceDesk->id,
            'current_area' => $serviceDesk->slug,
            'assigned_to_id' => $support->id,
            'status' => Ticket::STATUS_ANALYSIS,
        ]);

        $response = $this->actingAs($support)
            ->from(route('support.tickets.show', $ticket, false))
            ->post(route('support.tickets.transfer', $ticket), [
                'target_area_id' => $infrastructure->id,
                'note' => 'Encaminhando para infraestrutura.',
            ]);

        $response->assertRedirect(route('support.tickets.show', $ticket, false));

        $ticket->refresh();

        $this->assertSame($infrastructure->id, $ticket->area_id);
        $this->assertSame(Ticket::STATUS_PENDING, $ticket->status);
        $this->assertNull($ticket->assigned_to_id);
    }

    private function createArea(string $slug, string $name): SupportArea
    {
        return SupportArea::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'description' => $name,
                'is_active' => true,
            ]
        );
    }

    private function createSubject(int $category, string $name): SupportSubject
    {
        return SupportSubject::updateOrCreate(
            [
                'category' => $category,
                'name' => $name,
            ],
            [
                'is_active' => true,
            ]
        );
    }

    private function createTicket(User $requester, array $attributes = []): Ticket
    {
        $serviceDesk = SupportArea::firstOrCreate(
            ['slug' => 'service_desk'],
            [
                'name' => 'Service Desk',
                'description' => 'Triagem inicial e atendimento de primeiro nível.',
                'is_active' => true,
            ]
        );

        return Ticket::create(array_merge([
            'requester_id' => $requester->id,
            'subject' => 'Impressora com erro',
            'description' => 'A impressora da recepção parou de responder após a atualização do sistema.',
            'area_id' => $serviceDesk->id,
            'current_area' => $serviceDesk->slug,
            'status' => Ticket::STATUS_OPEN,
        ], $attributes));
    }
}
