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
        Schema::create('incubacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lote_ovos_id')->nullable();
            $table->unsignedBigInteger('tipo_ave_id')->nullable(); // No SQL, é nullable, mas no modelo é required. Mantendo como no SQL.
            $table->unsignedBigInteger('postura_ovo_id')->nullable(); // FK será adicionada em outra migration
            $table->date('data_entrada_incubadora');
            $table->date('data_prevista_eclosao'); // No SQL é NOT NULL
            $table->integer('quantidade_ovos');
            $table->integer('quantidade_eclodidos')->nullable(); // No SQL é NULLABLE
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            // Foreign keys que não causam dependência circular imediata
            $table->foreign('lote_ovos_id')->references('id')->on('lotes')->onDelete('set null');
            $table->foreign('tipo_ave_id')->references('id')->on('tipos_aves'); // Sem onDelete('cascade') no SQL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incubacoes');
    }
};
