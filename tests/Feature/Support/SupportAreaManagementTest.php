<?php

namespace Tests\Feature\Support;

use App\Models\SupportArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportAreaManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_support_area_and_assign_users(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('support.areas.store'), [
            'name' => 'Operações',
            'slug' => 'operacoes',
            'description' => 'Atendimento operacional',
            'is_active' => true,
            'user_ids' => [$userA->id, $userB->id],
        ]);

        $response->assertRedirect();

        $area = SupportArea::query()->where('slug', 'operacoes')->firstOrFail();

        $this->assertSame('Operações', $area->name);
        $this->assertTrue($area->is_active);
        $this->assertDatabaseHas('support_area_user', [
            'support_area_id' => $area->id,
            'user_id' => $userA->id,
        ]);
        $this->assertDatabaseHas('support_area_user', [
            'support_area_id' => $area->id,
            'user_id' => $userB->id,
        ]);
    }
}
