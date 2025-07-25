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
        Schema::table('vendas', function (Blueprint $table) {
            // Coluna para o ID do usuário (vendedor)
            // Assumindo que a tabela de usuários é 'users' e a chave primária é 'id'
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('reserva_id');

            // Percentual de comissão (30% fixo, mas mantemos a coluna para flexibilidade futura)
            $table->decimal('comissao_percentual', 5, 2)->default(0.00)->after('user_id');

            // Flag para indicar se a comissão foi paga/registrada
            $table->boolean('comissao_paga')->default(false)->after('comissao_percentual');

            // Coluna para vincular à despesa de comissão gerada
            // Assumindo que a tabela de despesas é 'despesas' e a chave primária é 'id'
            $table->foreignId('despesa_id')->nullable()->constrained('despesas')->onDelete('set null')->after('comissao_paga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            // Remover chaves estrangeiras primeiro
            $table->dropForeign(['user_id']);
            $table->dropForeign(['despesa_id']);

            // Remover colunas
            $table->dropColumn('user_id');
            $table->dropColumn('comissao_percentual');
            $table->dropColumn('comissao_paga');
            $table->dropColumn('despesa_id');
        });
    }
};
