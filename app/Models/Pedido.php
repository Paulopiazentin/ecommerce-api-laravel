<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'status',
        'valor_total',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'data_pedido' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'id_pedido', 'id_pedido');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'id_pedido', 'id_pedido');
    }
}