<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itens_pedido', function (Blueprint $table) {
            $table->unsignedInteger('id_pedido');
            $table->unsignedInteger('id_produto');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 10, 2);

            $table->primary(['id_pedido', 'id_produto']);

            $table->foreign('id_pedido')->references('id_pedido')->on('pedidos')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_produto')->references('id_produto')->on('produtos')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->index('id_produto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_pedido');
    }
};