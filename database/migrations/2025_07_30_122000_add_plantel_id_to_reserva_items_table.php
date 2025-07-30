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
        Schema::table('reserva_items', function (Blueprint $table) {
            // Adiciona a coluna plantel_id, nullable, e chave estrangeira
            // Colocamos 'after('ave_id')' para manter a ordem lógica.
            $table->unsignedBigInteger('plantel_id')->nullable()->after('ave_id');
            $table->foreign('plantel_id')->references('id')->on('plantel')->onDelete('set null');

            // O campo 'quantidade' já existe na tabela reserva_items e pode ser usado para a quantidade do plantel.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserva_items', function (Blueprint $table) {
            // Remove a chave estrangeira primeiro
            $table->dropForeign(['plantel_id']);
            // Remove a coluna
            $table->dropColumn('plantel_id');
        });
    }
};
