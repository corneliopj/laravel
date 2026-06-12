<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('suinos')) {
            // Se a tabela já existe, apenas adicionamos as colunas novas
            Schema::table('suinos', function (Blueprint $table) {
                if (!Schema::hasColumn('suinos', 'tipo')) {
                    $table->enum('tipo', ['matriz', 'transitorio'])->default('transitorio')->after('matricula');
                }
                if (!Schema::hasColumn('suinos', 'lote_id')) {
                    $table->unsignedBigInteger('lote_id')->nullable()->after('tipo');
                    $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('set null');
                }
                if (!Schema::hasColumn('suinos', 'variacao_id')) {
                    $table->unsignedBigInteger('variacao_id')->nullable()->after('lote_id');
                    $table->foreign('variacao_id')->references('id')->on('variacoes')->onDelete('set null');
                }
                if (!Schema::hasColumn('suinos', 'codigo_validacao_certidao')) {
                    $table->string('codigo_validacao_certidao')->nullable()->after('data_inativado');
                }
            });
        } else {
            // Se não existe, cria do zero
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

                $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('set null');
                $table->foreign('variacao_id')->references('id')->on('variacoes')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('suinos');
    }
};
