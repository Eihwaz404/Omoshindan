<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $allPermissions = collect(config('permissions.groups', []))
            ->flatMap(fn (array $group) => array_keys($group['permissions'] ?? []))
            ->values()
            ->all();

        User::updateOrCreate(
            ['email' => 'rick@eihwaz.com.br'],
            [
                'name' => 'Rick',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'role' => 'super_admin',
                'is_active' => true,
                'permissions' => $allPermissions,
            ],
        );
    }
}
