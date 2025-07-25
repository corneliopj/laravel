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
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('data_venda'); // Data e hora da venda
            $table->decimal('valor_total', 10, 2); // Soma dos itens antes do desconto
            $table->decimal('desconto', 10, 2)->default(0.00); // Valor do desconto aplicado
            $table->decimal('valor_final', 10, 2); // Valor final após o desconto
            $table->string('metodo_pagamento')->nullable(); // Ex: 'Dinheiro', 'Cartão', 'Pix'
            $table->text('observacoes')->nullable(); // Observações da venda
            $table->enum('status', ['concluida', 'pendente', 'cancelada'])->default('concluida'); // Status da venda
            // Se houver um módulo de clientes, customer_id seria uma foreign key. Por enquanto, pode ser nullable.
            // $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes(); // Para soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
