<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(
            Produto::class,
            'produtos_categorias',
            'id_categoria',
            'id_produto'
        );
    }
}