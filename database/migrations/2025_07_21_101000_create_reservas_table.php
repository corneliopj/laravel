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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_reserva')->unique(); // Número único para a reserva/orçamento
            $table->dateTime('data_reserva'); // Data de criação da reserva
            $table->dateTime('data_prevista_entrega')->nullable(); // Data para entrega do pedido
            $table->date('data_vencimento_proposta')->nullable(); // Data de validade do orçamento
            $table->decimal('valor_total', 10, 2); // Soma dos itens da reserva
            $table->decimal('pagamento_parcial', 10, 2)->default(0.00); // Valor de entrada/sinal
            $table->string('nome_cliente')->nullable(); // Nome do cliente (se não tiver módulo de clientes)
            $table->string('contato_cliente')->nullable(); // Contato do cliente (telefone/email)
            $table->text('observacoes')->nullable(); // Observações da reserva
            $table->enum('status', ['pendente', 'confirmada', 'cancelada', 'convertida_venda'])->default('pendente'); // Status da reserva
            $table->timestamps();
            $table->softDeletes(); // Para soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
