<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Cliente extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'endereco',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'id_cliente', 'id_cliente');
    }
}