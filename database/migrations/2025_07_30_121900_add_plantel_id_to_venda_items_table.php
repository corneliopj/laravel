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
        Schema::table('venda_items', function (Blueprint $table) {
            // Adiciona a coluna plantel_id, nullable, e chave estrangeira
            // Colocamos 'after('ave_id')' para manter a ordem lógica.
            $table->unsignedBigInteger('plantel_id')->nullable()->after('ave_id');
            $table->foreign('plantel_id')->references('id')->on('plantel')->onDelete('set null');

            // O campo 'quantidade' já existe na tabela venda_items e pode ser usado para a quantidade do plantel.
            // Se você precisar de um campo separado para a quantidade de aves dentro de um item de plantel,
            // poderíamos adicionar 'quantidade_plantel_item' ou algo similar, mas 'quantidade' deve ser suficiente.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venda_items', function (Blueprint $table) {
            // Remove a chave estrangeira primeiro
            $table->dropForeign(['plantel_id']);
            // Remove a coluna
            $table->dropColumn('plantel_id');
        });
    }
};
