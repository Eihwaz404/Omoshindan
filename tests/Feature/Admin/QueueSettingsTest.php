<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_queue_and_dashboard_refresh_intervals_and_support_pages_use_them(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Temp. Att. Fila')
            ->assertSee('Temp. Att. Dashboard')
            ->assertSee('Jornada de trabalho')
            ->assertSee('Prioridades');

        $this->actingAs($admin)
            ->post(route('admin.settings.update'), [
                'queue_refresh_interval_seconds' => 45,
                'dashboard_refresh_interval_seconds' => 20,
                'work_schedule' => [
                    'monday' => [
                        'start' => '08:00',
                        'end' => '17:00',
                        'lunch_start' => '12:00',
                        'lunch_end' => '13:00',
                    ],
                    'tuesday' => [
                        'start' => '08:00',
                        'end' => '17:00',
                        'lunch_start' => '12:00',
                        'lunch_end' => '13:00',
                    ],
                    'wednesday' => [
                        'start' => '08:00',
                        'end' => '17:00',
                        'lunch_start' => '12:00',
                        'lunch_end' => '13:00',
                    ],
                    'thursday' => [
                        'start' => '08:00',
                        'end' => '17:00',
                        'lunch_start' => '12:00',
                        'lunch_end' => '13:00',
                    ],
                    'friday' => [
                        'start' => '08:00',
                        'end' => '17:00',
                        'lunch_start' => '12:00',
                        'lunch_end' => '13:00',
                    ],
                ],
                'priority_sla' => [
                    'low' => ['minutes' => 1440],
                    'normal' => ['minutes' => 720],
                    'medium' => ['minutes' => 240],
                    'high' => ['minutes' => 60],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('system_settings', [
            'key' => 'queue_refresh_interval_seconds',
            'value' => '45',
        ]);
        $this->assertDatabaseHas('system_settings', [
            'key' => 'dashboard_refresh_interval_seconds',
            'value' => '20',
        ]);
        $this->assertDatabaseHas('system_settings', [
            'key' => 'work_schedule_monday_start',
            'value' => '08:00',
        ]);
        $this->assertDatabaseHas('system_settings', [
            'key' => 'work_schedule_friday_lunch_end',
            'value' => '13:00',
        ]);
        $this->assertDatabaseHas('system_settings', [
            'key' => 'priority_high_resolution_minutes',
            'value' => '60',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('support.tickets.index'))
            ->assertOk()
            ->assertSee('wire:poll.45s', false);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('wire:poll.20s', false);
    }
}
