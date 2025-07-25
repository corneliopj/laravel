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
        Schema::table('incubacoes', function (Blueprint $table) {
            // Adiciona a coluna 'chocadeira' como ENUM
            if (!Schema::hasColumn('incubacoes', 'chocadeira')) {
                $table->enum('chocadeira', ['130-1', '130-2', '130-3', 'Caixa', 'Garnisé', 'Perua', 'Galinha'])->nullable()->after('observacoes');
            }
            // Adiciona as colunas para contagem de ovos
            if (!Schema::hasColumn('incubacoes', 'quantidade_inferteis')) {
                $table->integer('quantidade_inferteis')->default(0)->after('chocadeira');
            }
            if (!Schema::hasColumn('incubacoes', 'quantidade_infectados')) {
                $table->integer('quantidade_infectados')->default(0)->after('quantidade_inferteis');
            }
            if (!Schema::hasColumn('incubacoes', 'quantidade_mortos')) {
                $table->integer('quantidade_mortos')->default(0)->after('quantidade_infectados');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incubacoes', function (Blueprint $table) {
            // Remove as colunas em caso de rollback
            if (Schema::hasColumn('incubacoes', 'quantidade_mortos')) {
                $table->dropColumn('quantidade_mortos');
            }
            if (Schema::hasColumn('incubacoes', 'quantidade_infectados')) {
                $table->dropColumn('quantidade_infectados');
            }
            if (Schema::hasColumn('incubacoes', 'quantidade_inferteis')) {
                $table->dropColumn('quantidade_inferteis');
            }
            if (Schema::hasColumn('incubacoes', 'chocadeira')) {
                $table->dropColumn('chocadeira');
            }
        });
    }
};
