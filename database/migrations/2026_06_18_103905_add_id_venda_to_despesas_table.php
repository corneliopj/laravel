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
        Schema::table('despesas', function (Blueprint $table) {
            // FK nullable para a venda que originou esta despesa (comissão)
            $table->foreignId('id_venda')
                ->nullable()
                ->constrained('vendas')
                ->onDelete('set null')
                ->after('categoria_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->dropForeign(['id_venda']);
            $table->dropColumn('id_venda');
        });
    }
};