<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adiciona as colunas polimórficas se elas não existirem
        Schema::table('mortes', function (Blueprint $table) {
            if (!Schema::hasColumn('mortes', 'animal_id')) {
                $table->unsignedBigInteger('animal_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('mortes', 'animal_type')) {
                $table->string('animal_type')->nullable()->after('animal_id');
            }
        });

        // 2. Se a coluna ave_id ainda existe, precisamos migrar os dados e removê-la
        if (Schema::hasColumn('mortes', 'ave_id')) {
            
            // Remove a Foreign Key usando SQL bruto para que o try-catch funcione instantaneamente
            try {
                DB::statement('ALTER TABLE mortes DROP FOREIGN KEY mortes_ave_id_foreign');
            } catch (\Exception $e) {
                // Se a FK não existir, apenas ignoramos e seguimos em frente
            }

            // Migra os dados de ave_id para animal_id
            DB::table('mortes')->update([
                'animal_id' => DB::raw('ave_id'),
                'animal_type' => 'App\Models\Ave'
            ]);

            // Remove a coluna ave_id
            Schema::table('mortes', function (Blueprint $table) {
                $table->dropColumn('ave_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('mortes', function (Blueprint $table) {
            $table->unsignedBigInteger('ave_id')->nullable();
        });

        DB::table('mortes')->update([
            'ave_id' => DB::raw('animal_id')
        ]);

        Schema::table('mortes', function (Blueprint $table) {
            $table->dropColumn(['animal_id', 'animal_type']);
        });
    }
};
