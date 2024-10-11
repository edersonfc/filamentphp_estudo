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
        Schema::table('fornecedors', function (Blueprint $table) {
            $table->string('nome')->after('id');
            $table->string('email')->unique()->after('nome');
            $table->string('telefone')->after('email');
            $table->string('endereco')->after('telefone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fornecedors', function (Blueprint $table) {
            $table->dropColumn(['nome', 'email', 'telefone', 'endereco']);
        });
    }
};
