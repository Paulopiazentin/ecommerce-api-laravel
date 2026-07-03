<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProdutoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Produto::query()->with('categorias');

        // Filtro por nome (busca parcial, case-insensitive)
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->input('nome') . '%');
        }

        // Filtro por categoria (id da categoria)
        if ($request->filled('categoria_id')) {
            $categoriaId = $request->input('categoria_id');
            $query->whereHas('categorias', function ($q) use ($categoriaId) {
                $q->where('categorias.id_categoria', $categoriaId);
            });
        }

        // Filtro por faixa de preço
        if ($request->filled('preco_min')) {
            $query->where('preco', '>=', $request->input('preco_min'));
        }
        if ($request->filled('preco_max')) {
            $query->where('preco', '<=', $request->input('preco_max'));
        }

        $produtos = $query->paginate(15);

        return response()->json($produtos);
    }
}