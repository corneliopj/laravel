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
        Schema::create('receitas', function (Blueprint $table) {
            $table->id();
            // Removido: user_id não é mais associado a uma receita específica
            $table->string('descricao');
            // Valor da receita, com precisão para valores monetários
            $table->decimal('valor', 10, 2);
            // Chave estrangeira para a categoria da receita
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict'); // Não permite deletar categoria se houver receitas associadas
            $table->date('data'); // Data em que a receita foi recebida
            $table->timestamps();
            $table->softDeletes(); // Para soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receitas');
    }
};

