<?php

use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;

uses(RefreshDatabase::class);

it('usa la tabla categorias y puede crearse con nombre/descripcion/activa', function () {
    $cat = Categoria::create([
        'nombre'      => 'Hogar y Cocina',
        'descripcion' => 'Artículos del hogar',
        'activa'      => true,
    ]);

    expect($cat->id)->toBeInt();
    expect($cat->nombre)->toBe('Hogar y Cocina');
    expect($cat->activa)->toBeTrue();
});

it('expone la relación productos como hasMany con fk categoria_id', function () {
    $rel = (new Categoria())->productos();

    expect($rel)->toBeInstanceOf(HasMany::class);
    expect($rel->getForeignKeyName())->toBe('categoria_id');
});
