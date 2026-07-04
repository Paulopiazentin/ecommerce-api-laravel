<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

     public function rules(): array
    {
        return [
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.id_produto' => ['required', 'integer', 'exists:produtos,id_produto'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'itens.required' => 'O pedido precisa ter ao menos um item.',
            'itens.min' => 'O pedido precisa ter ao menos um item.',
            'itens.*.id_produto.exists' => 'Um dos produtos informados não existe.',
            'itens.*.quantidade.min' => 'A quantidade deve ser de pelo menos 1.',
        ];
    }
}