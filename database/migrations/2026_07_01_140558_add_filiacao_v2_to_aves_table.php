<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aves', function (Blueprint $table) {
            $table->unsignedBigInteger('pai_id')->nullable();
            $table->unsignedBigInteger('mae_id')->nullable();
            $table->string('criatorio_origem', 20)->nullable();
            $table->string('registro_abrasb', 15)->nullable();

            $table->foreign('pai_id')->references('id')->on('aves')->onDelete('set null');
            $table->foreign('mae_id')->references('id')->on('aves')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('aves', function (Blueprint $table) {
            $table->dropForeign(['pai_id']);
            $table->dropForeign(['mae_id']);
            $table->dropColumn(['pai_id', 'mae_id', 'criatorio_origem', 'registro_abrasb']);
        });
    }
};
