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
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('value', 10, 2);
            $table->foreignId('category_id')->constrained('categorias'); // Associa a uma categoria existente
            $table->enum('type', ['receita', 'despesa']); // Tipo: receita ou despesa
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']); // Frequência da recorrência
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Data final da recorrência (opcional)
            $table->date('next_due_date')->nullable(); // Próxima data de vencimento para geração
            $table->date('last_generated_date')->nullable(); // Última data em que a transação foi gerada
            $table->timestamps();
            $table->softDeletes(); // Para soft delete, se necessário
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
