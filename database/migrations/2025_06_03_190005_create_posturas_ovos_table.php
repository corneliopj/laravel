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
        Schema::create('posturas_ovos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acasalamento_id'); // FK será adicionada em outra migration
            $table->date('data_inicio_postura');
            $table->date('data_fim_postura')->nullable();
            $table->integer('quantidade_ovos')->default(0);
            $table->text('observacoes')->nullable();
            $table->boolean('encerrada')->default(false); // Adicionado conforme o SQL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posturas_ovos');
    }
};
