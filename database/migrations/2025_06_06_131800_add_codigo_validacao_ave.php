<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aves', function (Blueprint $table) {
            // Adiciona a coluna 'codigo_validacao_certidao' como string, nullable e unique
            $table->string('codigo_validacao_certidao', 16)->nullable()->unique()->after('foto_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aves', function (Blueprint $table) {
            // Remove a coluna caso a migração seja revertida
            $table->dropColumn('codigo_validacao_certidao');
        });
    }
};
