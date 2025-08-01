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
        Schema::create('transacoes_recorrentes', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->enum('tipo', ['receita', 'despesa']);
            $table->decimal('valor', 10, 2);
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->enum('frequencia', ['diaria', 'semanal', 'mensal', 'anual']);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Índices para melhor performance
            $table->index(['tipo', 'ativo']);
            $table->index(['data_inicio', 'data_fim']);
            $table->index(['frequencia', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes_recorrentes');
    }
};

