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
        Schema::create('reserva_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->onDelete('cascade'); // Vincula ao ID da reserva
            $table->string('descricao_item'); // Descrição do item (para itens genéricos ou nome da ave)
            $table->foreignId('ave_id')->nullable()->constrained('aves')->onDelete('set null'); // Opcional: ID da ave reservada
            $table->integer('quantidade');
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('valor_total_item', 10, 2); // Quantidade * Preço Unitário
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_items');
    }
};
