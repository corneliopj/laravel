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
        Schema::create('aves', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->unique();
            $table->string('validation_code', 36)->nullable()->unique(); // UUID tem 36 caracteres
            $table->unsignedBigInteger('tipo_ave_id');
            $table->unsignedBigInteger('variacao_id')->nullable();
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->unsignedBigInteger('incubacao_id')->nullable(); // Será adicionada FK em outra migration
            $table->date('data_eclosao');
            $table->string('sexo')->default('Não identificado'); // Corresponde ao SQL
            $table->string('foto_path')->nullable();
            $table->boolean('vendavel')->default(true);
            $table->boolean('ativo')->default(true);
            $table->dateTime('data_inativado')->nullable(); // Corresponde ao SQL
            $table->timestamps();
            $table->softDeletes(); // Corresponde ao SQL

            // Foreign keys que não causam dependência circular imediata
            $table->foreign('tipo_ave_id')->references('id')->on('tipos_aves'); // Sem onDelete('cascade') no SQL
            $table->foreign('variacao_id')->references('id')->on('variacoes')->onDelete('set null');
            $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aves');
    }
};
