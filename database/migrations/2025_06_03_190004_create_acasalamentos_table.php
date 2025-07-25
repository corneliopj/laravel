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
        Schema::create('acasalamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('macho_id'); // FK será adicionada em outra migration
            $table->unsignedBigInteger('femea_id'); // FK será adicionada em outra migration
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Unique key composta conforme o SQL
            $table->unique(['macho_id', 'femea_id', 'data_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acasalamentos');
    }
};
