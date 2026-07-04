<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePedidoRequest;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    public function store(StorePedidoRequest $request): JsonResponse
    {
        $dadosValidados = $request->validated();

        $pedido = DB::transaction(function () use ($dadosValidados) {
            $pedido = Pedido::create([
                'id_cliente' => $dadosValidados['id_cliente'],
                'status' => 'pendente',
                'valor_total' => 0,
            ]);

            $valorTotal = 0;

            foreach ($dadosValidados['itens'] as $item) {
                $produto = Produto::where('id_produto', $item['id_produto'])
                    ->lockForUpdate()
                    ->first();

                if ($produto->estoque < $item['quantidade']) {
                    throw new \RuntimeException(
                        "Estoque insuficiente para o produto '{$produto->nome}'. Disponível: {$produto->estoque}, solicitado: {$item['quantidade']}."
                    );
                }

                $precoUnitario = $produto->preco;
                $subtotal = $precoUnitario * $item['quantidade'];
                $valorTotal += $subtotal;

                $pedido->itens()->create([
                    'id_produto' => $item['id_produto'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $precoUnitario,
                ]);

                $produto->decrement('estoque', $item['quantidade']);
            }

            $pedido->update(['valor_total' => $valorTotal]);

            return $pedido;
        });

        Log::info('Pedido criado com sucesso', [
            'id_pedido' => $pedido->id_pedido,
            'id_cliente' => $pedido->id_cliente,
            'valor_total' => $pedido->valor_total,
        ]);

        $pedido->load('itens.produto', 'cliente');

        return response()->json($pedido, 201);
    }
}