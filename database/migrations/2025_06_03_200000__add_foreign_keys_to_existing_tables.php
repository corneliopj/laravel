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
        Schema::table('acasalamentos', function (Blueprint $table) {
            // Adiciona chaves estrangeiras para 'aves' em 'acasalamentos'
            if (!Schema::hasColumn('acasalamentos', 'macho_id_foreign')) { // Verifica se a FK já existe
                $table->foreign('macho_id')->references('id')->on('aves')->onDelete('cascade');
            }
            if (!Schema::hasColumn('acasalamentos', 'femea_id_foreign')) { // Verifica se a FK já existe
                $table->foreign('femea_id')->references('id')->on('aves')->onDelete('cascade');
            }
        });

        Schema::table('posturas_ovos', function (Blueprint $table) {
            // Adiciona chave estrangeira para 'acasalamentos' em 'posturas_ovos'
            if (!Schema::hasColumn('posturas_ovos', 'acasalamento_id_foreign')) {
                $table->foreign('acasalamento_id')->references('id')->on('acasalamentos')->onDelete('cascade');
            }
        });

        Schema::table('incubacoes', function (Blueprint $table) {
            // Adiciona chave estrangeira para 'posturas_ovos' em 'incubacoes'
            if (!Schema::hasColumn('incubacoes', 'postura_ovo_id_foreign')) {
                $table->foreign('postura_ovo_id')->references('id')->on('posturas_ovos')->onDelete('set null');
            }
        });

        Schema::table('aves', function (Blueprint $table) {
            // Adiciona chave estrangeira para 'incubacoes' em 'aves'
            if (!Schema::hasColumn('aves', 'incubacao_id_foreign')) {
                $table->foreign('incubacao_id')->references('id')->on('incubacoes')->onDelete('set null');
            }
        });

        Schema::table('mortes', function (Blueprint $table) {
            // Adiciona chave estrangeira para 'aves' em 'mortes'
            if (!Schema::hasColumn('mortes', 'ave_id_foreign')) {
                $table->foreign('ave_id')->references('id')->on('aves')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acasalamentos', function (Blueprint $table) {
            $table->dropForeign(['macho_id']);
            $table->dropForeign(['femea_id']);
        });

        Schema::table('posturas_ovos', function (Blueprint $table) {
            $table->dropForeign(['acasalamento_id']);
        });

        Schema::table('incubacoes', function (Blueprint $table) {
            $table->dropForeign(['postura_ovo_id']);
        });

        Schema::table('aves', function (Blueprint $table) {
            $table->dropForeign(['incubacao_id']);
        });

        Schema::table('mortes', function (Blueprint $table) {
            $table->dropForeign(['ave_id']);
        });
    }
};
