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
        Schema::create('movimentacoes_plantel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantel_id')->constrained('plantel')->onDelete('cascade'); // FK para a nova tabela 'plantel'
            $table->enum('tipo_movimentacao', ['entrada', 'saida_venda', 'saida_morte', 'saida_consumo', 'saida_doacao', 'saida_descarte', 'outros']);
            $table->integer('quantidade');
            $table->date('data_movimentacao');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_plantel');
    }
};
