<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->increments('id_produto');
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2);
            $table->integer('estoque')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};