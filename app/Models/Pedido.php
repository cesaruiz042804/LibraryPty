<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
        
    protected $table = 'pedidos';
    protected $fillable = [
        'id',
        'user_id',
        'libro_id',
        'stock',
        'fecha_pedido',
        'fecha_entrega',
        'libro_id',
        'user_id',
    ];
}
