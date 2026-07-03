<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'estoque',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
    ];

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            Categoria::class,
            'produtos_categorias',
            'id_produto',
            'id_categoria'
        );
    }

    public function itensPedido(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'id_produto', 'id_produto');
    }
}