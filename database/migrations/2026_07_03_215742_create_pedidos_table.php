<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->increments('id_pedido');
            $table->unsignedInteger('id_cliente');
            $table->dateTime('data_pedido')->useCurrent();
            $table->string('status', 50)->default('pendente');
            $table->decimal('valor_total', 10, 2)->default(0);

            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->index('id_cliente');
            $table->index('status');
            $table->index('data_pedido');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
