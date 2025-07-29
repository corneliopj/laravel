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
        Schema::create('plantel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_ave_id')->constrained('tipos_aves')->onDelete('restrict'); // FK para tipos_aves
            $table->string('identificacao_grupo')->unique(); // Nome/código único do grupo (ex: "Codorneiras Sala 1")
            $table->date('data_formacao'); // Quando este grupo foi formado/nascido/adquirido
            $table->integer('quantidade_inicial'); // Quantidade de aves que iniciaram este grupo
            $table->boolean('ativo')->default(true); // O grupo está ativo?
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantel');
    }
};
