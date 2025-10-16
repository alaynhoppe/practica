<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    /** @use HasFactory<\Database\Factories\CategoriaFactory> */
    use HasFactory;
    //nombre de la tabla
    protected $table = 'categorias';
    //habilitar la insercion masica de datos
    protected $fillable = [
        'nombre',
        'descripcion',
        'activa',
    ];

    //funcion publica para relacionar con la tabla productos
    public function productos ()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
