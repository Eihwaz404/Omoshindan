<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SupportSubject;
use App\Models\SystemSetting;
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

        $subjects = [
            [1, 'Acesso ao sistema'],
            [1, 'Erro de autenticação'],
            [1, 'Senha expirada'],
            [2, 'Instabilidade no sistema'],
            [2, 'Tela em branco'],
            [2, 'Falha em relatório'],
            [3, 'Configuração de rede'],
            [3, 'Sem internet'],
            [4, 'Novo recurso'],
            [4, 'Ajuste de processo'],
        ];

        foreach ($subjects as [$category, $name]) {
            SupportSubject::updateOrCreate(
                [
                    'category' => $category,
                    'name' => $name,
                ],
                [
                    'is_active' => true,
                ]
            );
        }

        SystemSetting::query()->firstOrCreate(
            ['key' => 'queue_refresh_interval_seconds'],
            ['value' => '15'],
        );

        SystemSetting::query()->firstOrCreate(
            ['key' => 'dashboard_refresh_interval_seconds'],
            ['value' => '30'],
        );

        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $dayKey) {
            SystemSetting::query()->firstOrCreate(
                ['key' => "work_schedule_{$dayKey}_start"],
                ['value' => '08:00'],
            );
            SystemSetting::query()->firstOrCreate(
                ['key' => "work_schedule_{$dayKey}_end"],
                ['value' => '17:00'],
            );
            SystemSetting::query()->firstOrCreate(
                ['key' => "work_schedule_{$dayKey}_lunch_start"],
                ['value' => '12:00'],
            );
            SystemSetting::query()->firstOrCreate(
                ['key' => "work_schedule_{$dayKey}_lunch_end"],
                ['value' => '13:00'],
            );
        }

        foreach ([
            'low' => 1440,
            'normal' => 720,
            'medium' => 240,
            'high' => 60,
        ] as $priority => $minutes) {
            SystemSetting::query()->firstOrCreate(
                ['key' => "priority_{$priority}_resolution_minutes"],
                ['value' => (string) $minutes],
            );
        }
    }
}
