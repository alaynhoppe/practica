<?php

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lista productos paginados con estructura', function () {
    Producto::factory()->count(3)->create();

    $res = $this->getJson('/api/todos-los-productos');

    // Tu index usa ->paginate() directo (sin Resource), así que validamos
    // la estructura nativa del paginator (no existe "meta")
    $res->assertStatus(200)->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'categoria_id',
                'nombre',
                'sku',
                'stock',
                'precio',
                'activo',
                'created_at',
                'updated_at',
                // Si quieres validar que viene la relación cargada:
                // 'categoria' => ['id', 'nombre']
            ],
        ],
        'current_page',
        'first_page_url',
        'from',
        'last_page',
        'last_page_url',
        'links',
        'next_page_url',
        'path',
        'per_page',
        'prev_page_url',
        'to',
        'total',
    ]);
});

it('crea un producto (201) y lo persiste', function () {
    $categoria = Categoria::factory()->create();

    $payload = [
        'categoria_id' => $categoria->id,
        'nombre'       => 'Producto Nuevo',
        'sku'          => 'SKU-ABC01',
        'stock'        => 10,
        'precio'       => 19.99,
        'activo'       => true,
    ];

    $res = $this->postJson('/api/guardar-producto', $payload);

    $res->assertStatus(201)
        ->assertJsonFragment([
            'nombre'       => 'Producto Nuevo',
            'sku'          => 'SKU-ABC01',
            'categoria_id' => $categoria->id,
            'stock'        => 10,
            'activo'       => true,
        ]);

    $this->assertDatabaseHas('productos', [
        'nombre'       => 'Producto Nuevo',
        'sku'          => 'SKU-ABC01',
        'categoria_id' => $categoria->id,
        'stock'        => 10,
        'precio'       => 19.99,
        'activo'       => true,
    ]);
});

it('rechaza crear producto inválido (422) por reglas de validación', function () {
    // Faltan campos obligatorios y hay valores inválidos
    $res = $this->postJson('/api/guardar-producto', [
        // 'categoria_id' ausente
        'nombre' => '',     // requerido
        'sku'    => '',     // requerido
        'stock'  => -1,     // min:0
        'precio' => -5,     // min:0
        // 'activo' es boolean opcional
    ]);

    $res->assertStatus(422)
        ->assertJsonValidationErrors([
            'categoria_id', 'nombre', 'sku', 'stock', 'precio'
        ]);
});

it('muestra un producto específico con su categoría', function () {
    $producto = Producto::factory()->create();

    $res = $this->getJson("/api/productos/{$producto->id}");

    $res->assertStatus(200)
        ->assertJsonFragment(['id' => $producto->id])
        ->assertJsonStructure([
            'id', 'categoria_id', 'nombre', 'sku', 'stock', 'precio', 'activo', 'created_at', 'updated_at',
            // El show() hace ->load('categoria'), validamos presencia de la relación:
            'categoria' => ['id', 'nombre'],
        ]);
});

it('actualiza un producto (200) y refleja los cambios', function () {
    $producto  = Producto::factory()->create();
    $categoria = Categoria::factory()->create();

    $payload = [
        'categoria_id' => $categoria->id,
        'nombre'       => 'Producto Editado',
        'sku'          => 'SKU-EDIT01',
        'stock'        => 25,
        'precio'       => 49.50,
        'activo'       => false,
    ];

    $res = $this->putJson("/api/productos/{$producto->id}", $payload);

    $res->assertStatus(200)
        ->assertJsonFragment([
            'id'           => $producto->id,
            'nombre'       => 'Producto Editado',
            'sku'          => 'SKU-EDIT01',
            'categoria_id' => $categoria->id,
            'stock'        => 25,
            'activo'       => false,
        ]);

    $this->assertDatabaseHas('productos', [
        'id'           => $producto->id,
        'nombre'       => 'Producto Editado',
        'sku'          => 'SKU-EDIT01',
        'categoria_id' => $categoria->id,
        'stock'        => 25,
        'precio'       => 49.50,
        'activo'       => false,
    ]);
});

it('elimina un producto (204) y no existe en DB', function () {
    $producto = Producto::factory()->create();

    $res = $this->deleteJson("/api/productos/{$producto->id}");

    $res->assertStatus(204);
    $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
});
