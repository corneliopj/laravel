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
        Schema::table('receitas', function (Blueprint $table) {
            // Adiciona a coluna recurring_transaction_id como chave estrangeira, nullable
            $table->foreignId('recurring_transaction_id')->nullable()->constrained('recurring_transactions')->onDelete('set null');
        });

        Schema::table('despesas', function (Blueprint $table) {
            // Adiciona a coluna recurring_transaction_id como chave estrangeira, nullable
            $table->foreignId('recurring_transaction_id')->nullable()->constrained('recurring_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receitas', function (Blueprint $table) {
            $table->dropForeign(['recurring_transaction_id']);
            $table->dropColumn('recurring_transaction_id');
        });

        Schema::table('despesas', function (Blueprint $table) {
            $table->dropForeign(['recurring_transaction_id']);
            $table->dropColumn('recurring_transaction_id');
        });
    }
};
