<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Este modelo representa un libro en la base de datos
class Libro extends Model
{
    use HasFactory;
    // Especifica la tabla asociada al modelo
    protected $table = 'libros';

    // Especifica los campos que pueden ser llenados masivamente
    // para proteger contra asignación masiva.
    protected $fillable = [
        'titulo',
        'autor',
        'anio_publicacion',
        'isbn',
        'sinopsis',
        'status',
        'stock',
        'stock_minimo',
        'precio_base',
        'precio_final',
        'category_id',
        'imagen',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
