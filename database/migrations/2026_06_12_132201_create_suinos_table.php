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
        Schema::create('suinos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->unique();
            $table->enum('tipo', ['matriz', 'transitorio'])->default('transitorio');
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->unsignedBigInteger('variacao_id')->nullable();
            $table->string('sexo', 10)->nullable();
            $table->boolean('vendavel')->default(true);
            $table->boolean('ativo')->default(true);
            $table->dateTime('data_inativado')->nullable();
            $table->date('data_venda')->nullable();
            $table->string('codigo_validacao_certidao')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('set null');
            $table->foreign('variacao_id')->references('id')->on('variacoes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suinos');
    }
};
