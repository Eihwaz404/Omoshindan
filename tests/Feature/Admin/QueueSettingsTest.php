<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_queue_refresh_interval_and_ticket_queue_uses_it(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Temp. Att. Fila');

        $this->actingAs($admin)
            ->post(route('admin.settings.update'), [
                'queue_refresh_interval_seconds' => 45,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('system_settings', [
            'key' => 'queue_refresh_interval_seconds',
            'value' => '45',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('support.tickets.index'))
            ->assertOk()
            ->assertSee('wire:poll.45s', false);
    }
}
