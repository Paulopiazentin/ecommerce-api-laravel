<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPedido extends Model
{
    use HasFactory;

    protected $table = 'itens_pedido';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'id_pedido',
        'id_produto',
        'quantidade',
        'preco_unitario',
    ];

    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'quantidade' => 'integer',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'id_produto');
    }
}