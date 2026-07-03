<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos_categorias', function (Blueprint $table) {
            $table->unsignedInteger('id_produto');
            $table->unsignedInteger('id_categoria');
            $table->primary(['id_produto', 'id_categoria']);

            $table->foreign('id_produto')->references('id_produto')->on('produtos')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_categoria')->references('id_categoria')->on('categorias')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos_categorias');
    }
};