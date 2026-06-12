<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mortes', function (Blueprint $table) {
            $table->unsignedBigInteger('animal_id')->nullable()->after('id');
            $table->string('animal_type')->nullable()->after('animal_id');
        });

        // Migra os dados existentes de ave_id para o novo formato polimórfico
        DB::table('mortes')->update([
            'animal_id' => DB::raw('ave_id'),
            'animal_type' => 'App\Models\Ave'
        ]);

        Schema::table('mortes', function (Blueprint $table) {
            $table->dropColumn('ave_id');
        });
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
