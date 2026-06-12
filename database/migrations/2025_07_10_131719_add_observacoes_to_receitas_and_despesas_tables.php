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
        Schema::table('receitas', function (Blueprint $table) {
            if (!Schema::hasColumn('receitas', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('data');
            }
        });

        Schema::table('despesas', function (Blueprint $table) {
            if (!Schema::hasColumn('despesas', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receitas', function (Blueprint $table) {
            if (Schema::hasColumn('receitas', 'observacoes')) {
                $table->dropColumn('observacoes');
            }
        });

        Schema::table('despesas', function (Blueprint $table) {
            if (Schema::hasColumn('despesas', 'observacoes')) {
                $table->dropColumn('observacoes');
            }
        });
    }
};