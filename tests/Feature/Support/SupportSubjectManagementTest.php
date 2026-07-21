<?php

namespace Tests\Feature\Support;

use App\Models\SupportSubject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportSubjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_and_list_support_subjects(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($admin)->post(route('support.subjects.store'), [
            'category' => 3,
            'name' => 'VPN instável',
            'is_active' => true,
        ]);

        $response->assertRedirect();

        $subject = SupportSubject::query()
            ->where('category', 3)
            ->where('name', 'VPN instável')
            ->firstOrFail();

        $this->assertSame(3, $subject->category);
        $this->assertSame('VPN instável', $subject->name);

        $this->actingAs($admin)
            ->get(route('support.subjects.index'))
            ->assertOk()
            ->assertSee('VPN instável');
    }
}
