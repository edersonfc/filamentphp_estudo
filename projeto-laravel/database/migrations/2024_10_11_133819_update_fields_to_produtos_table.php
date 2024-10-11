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
        Schema::table('produtos', function (Blueprint $table) {
            $table->renameColumn('fornecedor', 'nome_fornecedor');
        });
    }

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->renameColumn('nome_fornecedor', 'fornecedor');
        });
    }
};