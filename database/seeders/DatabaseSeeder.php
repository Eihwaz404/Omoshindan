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
    }
}
