<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('support_subjects')) {
            Schema::create('support_subjects', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('category');
                $table->string('name', 120);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['category', 'name']);
            });
        }

        if (! Schema::hasColumn('tickets', 'subject_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreignId('subject_id')
                    ->nullable()
                    ->after('assigned_to_id')
                    ->constrained('support_subjects')
                    ->nullOnDelete();
            });
        }

        $now = now();
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
            DB::table('support_subjects')->updateOrInsert([
                'category' => $category,
                'name' => $name,
            ], [
                'category' => $category,
                'name' => $name,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'subject_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropConstrainedForeignId('subject_id');
            });
        }

        Schema::dropIfExists('support_subjects');
    }
};
