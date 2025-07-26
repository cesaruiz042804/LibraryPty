<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    // Especifica los campos que pueden ser llenados masivamente
    // para proteger contra asignación masiva.
    protected $fillable = [
        'nombre',
    ];

    public function libros(): HasMany
    {
        return $this->hasMany(Libro::class);
    }
}
