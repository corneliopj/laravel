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
        Schema::create('mortes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ave_id')->unique(); // FK será adicionada em outra migration
            $table->date('data_morte');
            $table->string('causa')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mortes');
    }
};
