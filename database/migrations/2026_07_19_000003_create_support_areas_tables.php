<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('support_area_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_area_id')->constrained('support_areas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['support_area_id', 'user_id']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->after('assigned_to_id')->constrained('support_areas')->nullOnDelete();
        });

        Schema::table('ticket_events', function (Blueprint $table) {
            $table->foreignId('from_area_id')->nullable()->after('from_area')->constrained('support_areas')->nullOnDelete();
            $table->foreignId('to_area_id')->nullable()->after('to_area')->constrained('support_areas')->nullOnDelete();
        });

        $now = now();
        $seedAreas = [
            ['name' => 'Service Desk', 'slug' => 'service_desk', 'description' => 'Triagem inicial e atendimento de primeiro nível.'],
            ['name' => 'Sistemas', 'slug' => 'systems', 'description' => 'Demandas funcionais e integrações de sistemas.'],
            ['name' => 'Desenvolvimento', 'slug' => 'development', 'description' => 'Correções, evoluções e ajustes em código.'],
            ['name' => 'Infraestrutura', 'slug' => 'infrastructure', 'description' => 'Rede, servidores, acessos e ambiente.'],
        ];

        foreach ($seedAreas as $area) {
            DB::table('support_areas')->insert([
                'name' => $area['name'],
                'slug' => $area['slug'],
                'description' => $area['description'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $areaMap = DB::table('support_areas')->pluck('id', 'slug');

        DB::table('tickets')->orderBy('id')->chunkById(200, function ($tickets) use ($areaMap) {
            foreach ($tickets as $ticket) {
                $slug = $ticket->current_area;

                if (! $slug || ! $areaMap->has($slug)) {
                    $slug = config('support.routing.default_area', 'service_desk');
                }

                DB::table('tickets')
                    ->where('id', $ticket->id)
                    ->update([
                        'area_id' => $areaMap->get($slug),
                    ]);
            }
        });

        DB::table('ticket_events')->orderBy('id')->chunkById(200, function ($events) use ($areaMap) {
            foreach ($events as $event) {
                DB::table('ticket_events')
                    ->where('id', $event->id)
                    ->update([
                        'from_area_id' => $event->from_area && $areaMap->has($event->from_area) ? $areaMap->get($event->from_area) : null,
                        'to_area_id' => $event->to_area && $areaMap->has($event->to_area) ? $areaMap->get($event->to_area) : null,
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('from_area_id');
            $table->dropConstrainedForeignId('to_area_id');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('area_id');
        });

        Schema::dropIfExists('support_area_user');
        Schema::dropIfExists('support_areas');
    }
};
