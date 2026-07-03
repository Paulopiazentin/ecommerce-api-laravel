<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->increments('id_pagamento');
            $table->unsignedInteger('id_pedido');
            $table->string('forma_pagamento', 50);
            $table->decimal('valor', 10, 2);
            $table->dateTime('data_pagamento')->useCurrent();
            $table->string('status_pagamento', 50)->default('pendente');

            $table->foreign('id_pedido')->references('id_pedido')->on('pedidos')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index('id_pedido');
            $table->index('status_pagamento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};