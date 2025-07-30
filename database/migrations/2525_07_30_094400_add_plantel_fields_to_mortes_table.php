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
        Schema::table('mortes', function (Blueprint $table) {
            // Adiciona a coluna plantel_id, nullable, e chave estrangeira
            $table->unsignedBigInteger('plantel_id')->nullable()->after('ave_id');
            $table->foreign('plantel_id')->references('id')->on('plantel')->onDelete('set null');

            // Adiciona a coluna para quantidade de mortes se for de um plantel
            $table->integer('quantidade_mortes_plantel')->nullable()->after('plantel_id');

            // Garante que ou ave_id ou plantel_id seja preenchido (mas não ambos)
            // Isso pode ser feito com validação no Controller, mas é bom ter a intenção aqui.
            // Se ave_id já for nullable, não precisamos mudar.
            // Se ave_id não for nullable e você quiser que ele seja, você precisaria de outra migração.
            // Por enquanto, assumimos que ave_id já é nullable ou que você vai garantir exclusividade no controller.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mortes', function (Blueprint $table) {
            // Remove a chave estrangeira primeiro
            $table->dropForeign(['plantel_id']);
            // Remove as colunas
            $table->dropColumn('plantel_id');
            $table->dropColumn('quantidade_mortes_plantel');
        });
    }
};
