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
        Schema::create('variacoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->unsignedBigInteger('tipo_ave_id');
            $table->boolean('ativo')->default(true); // Corresponde ao SQL
            $table->timestamps(); // Corresponde ao SQL
            $table->softDeletes(); // Adicionado pois o modelo Variacao usa SoftDeletes

            // Foreign key para tipos_aves
            $table->foreign('tipo_ave_id')->references('id')->on('tipos_aves'); // Sem onDelete('cascade') no SQL, então manter padrão

            // Unique key composta conforme o SQL (nome, tipo_ave_id)
            $table->unique(['nome', 'tipo_ave_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variacoes');
    }
};
