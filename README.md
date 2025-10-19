# Práctica de Integración Back-End — Laravel + PostgreSQL

## Datos del estudiante
**Nombre:** Alayn Macías  
**Carrera:** Ingeniería en Tecnologías de la Información  
**Universidad:** Universidad Laica Eloy Alfaro de Manabí (ULEAM)  
**Materia:** APLCIACIONES WEB II

---

## Objetivo
Aplicar los conocimientos de conexión y manipulación de datos en un Sistema Gestor de Base de Datos (SGBD) desde el back-end, junto con los fundamentos de testing aplicados en Laravel, implementando operaciones CRUD, validaciones y pruebas automatizadas.

---

## Requisitos previos
- PHP 8.1 o superior  
- Composer  
- PostgreSQL instalado  
- Extensión `pdo_pgsql` habilitada en `php.ini`  
- Postman para probar endpoints

---

## Configuración de base de datos

Ya se cuenta con el usuario y la base creados previamente:

| Parámetro | Valor |
|------------|--------|
| **DB_CONNECTION** | pgsql |
| **DB_HOST** | 127.0.0.1 |
| **DB_PORT** | 5432 |
| **DB_DATABASE** | practica_alayn_appweb |
| **DB_USERNAME** | practicas |
| **DB_PASSWORD** | 12345 |

Archivo `.env` (fragmento relevante):

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=practica_alayn_appweb
DB_USERNAME=practicas
DB_PASSWORD=12345

Pasos de instalación y configuración
1️⃣ Crear el proyecto Laravel
composer create-project laravel/laravel practica_alayn
cd practica_alayn
php artisan key:generate

2️⃣ Crear modelos y migraciones
php artisan make:model Categoria -m -f
php artisan make:model Producto -m -f

3️⃣ Editar migraciones

create_categorias_table.php

Schema::create('categorias', function (Blueprint $table) {
    $table->id();
    $table->string('nombre', 255)->unique();
    $table->text('descripcion')->nullable();
    $table->timestamps();
});


create_productos_table.php

Schema::create('productos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
    $table->string('nombre', 255)->unique();
    $table->string('sku', 100)->unique();
    $table->integer('stock')->default(0);
    $table->decimal('precio', 10, 2)->default(0);
    $table->boolean('activo')->default(true);
    $table->timestamps();
});


Ejecutar migraciones:

php artisan migrate

Modelos Eloquent

app/Models/Categoria.php

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'descripcion'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}


app/Models/Producto.php

class Producto extends Model
{
    use HasFactory;
    protected $fillable = ['categoria_id','nombre','sku','stock','precio','activo'];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}

🚀 Controladores API
CategoriaController

app/Http/Controllers/Api/CategoriaController.php

public function store(Request $request)
{
    $data = $request->validate([
        'nombre' => 'required|string|max:255|unique:categorias,nombre',
        'descripcion' => 'nullable|string',
    ]);

    $categoria = Categoria::create($data);
    return response()->json($categoria, Response::HTTP_CREATED);
}

ProductoController

app/Http/Controllers/Api/ProductoController.php

public function store(Request $request)
{
    $data = $request->validate([
        'categoria_id' => 'required|exists:categorias,id',
        'nombre' => 'required|string|max:255|unique:productos,nombre',
        'sku' => 'required|string|max:100|unique:productos,sku',
        'stock' => 'required|integer|min:0',
        'precio' => 'required|numeric|min:0',
        'activo' => 'boolean',
    ]);

    $producto = Producto::create($data);
    return response()
        ->json($producto->load('categoria'), Response::HTTP_CREATED)
        ->setEncodingOptions(JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);
}

Rutas API

Archivo: routes/api.php

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;

// Categorías
Route::post('/guardar-categoria', [CategoriaController::class, 'store']);
Route::get('/todas-las-categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{categoria}', [CategoriaController::class, 'show']);
Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']);
Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy']);

// Productos
Route::post('/guardar-producto', [ProductoController::class, 'store']);
Route::get('/todos-los-productos', [ProductoController::class, 'index']);
Route::get('/productos/{producto}', [ProductoController::class, 'show']);
Route::put('/productos/{producto}', [ProductoController::class, 'update']);
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);

Ejemplos de uso en Postman
🔹 Crear categoría

POST http://127.0.0.1:8000/api/guardar-categoria

{
  "nombre": "Periféricos",
  "descripcion": "Accesorios de computadora"
}

🔹 Crear producto

POST http://127.0.0.1:8000/api/guardar-producto

{
  "categoria_id": 1,
  "nombre": "Teclado Aula F75 Pro",
  "sku": "AULA-F75PRO",
  "stock": 25,
  "precio": 79.90,
  "activo": true
}

🔹 Actualizar producto

PUT http://127.0.0.1:8000/api/productos/1

{
  "categoria_id": 1,
  "nombre": "Teclado Aula F75 Pro RGB",
  "sku": "AULA-F75PRO-RGB",
  "stock": 40,
  "precio": 89.99,
  "activo": true
}

Pruebas automatizadas (Pest / PHPUnit)

Archivo: .env.testing

APP_ENV=testing
APP_KEY=base64:TESTKEYGENERATE
APP_DEBUG=false
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
DB_FOREIGN_KEYS=true


Ejecutar:

php artisan serve
Testing con Pest / PHPUnit

En esta práctica se implementaron pruebas unitarias y pruebas de integración para validar el correcto funcionamiento del back-end desarrollado en Laravel.
Las pruebas garantizan la calidad del código, la integridad de los datos y el cumplimiento de las reglas de validación en cada endpoint.

Configuración del entorno de pruebas

Para mantener separadas las pruebas del entorno principal, se creó un archivo .env.test con una base de datos PostgreSQL exclusiva:

APP_ENV=test
APP_KEY=base64:u8oeKgBHTVUU+vlbM3aYN8ek0M0pYw84PDY5p/0aedA=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=practica_alayn_appweb_test
DB_USERNAME=practicas
DB_PASSWORD=12345


Antes de ejecutar las pruebas, se debe crear la base de datos de test:

CREATE DATABASE practica_alayn_appweb_test OWNER practicas;


Luego ejecutar:

php artisan migrate:fresh --env=test


Esto genera las tablas necesarias exclusivamente para el entorno de pruebas.

Archivos de prueba principales
tests/Unit/CategoriaModeTest.php

Este archivo contiene pruebas unitarias, enfocadas en verificar el funcionamiento interno del modelo Categoria.

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('usa la tabla categorias y puede crearse con nombre/descripcion/activa', function () {
    $cat = \App\Models\Categoria::create([
        'nombre' => 'Hogar y Cocina',
        'descripcion' => 'Artículos del hogar',
        'activa' => true,
    ]);

    expect($cat->id)->toBeInt();
    expect($cat->nombre)->toBe('Hogar y Cocina');
});

it('expone la relación productos como hasMany con fk categoria_id', function () {
    $rel = (new \App\Models\Categoria())->productos();
    expect($rel->getForeignKeyName())->toBe('categoria_id');
});


Explicación técnica:

Se prueba la capacidad del modelo para crear registros y asignar valores correctamente.

Se comprueba que la relación hasMany con Producto esté bien definida y que use la clave foránea correcta (categoria_id).

El uso de RefreshDatabase reinicia la base de datos antes de cada prueba, asegurando resultados limpios y consistentes.

tests/Feature/ProductoApiTest.php

Este archivo incluye pruebas de integración (o Feature Tests) que simulan llamadas reales a la API.

Se validan los endpoints principales:

Endpoint	Método	Descripción	Código esperado
/api/todos-los-productos	GET	Lista productos paginados	200
/api/guardar-producto	POST	Crea un nuevo producto	201
/api/productos/{id}	GET	Muestra un producto específico	200
/api/productos/{id}	PUT	Actualiza un producto existente	200
/api/productos/{id}	DELETE	Elimina un producto	204

Ejemplo simplificado:

it('crea un producto (201) y lo persiste', function () {
    $categoria = \App\Models\Categoria::factory()->create();

    $payload = [
        'categoria_id' => $categoria->id,
        'nombre' => 'Producto Nuevo',
        'sku' => 'SKU-ABC01',
        'stock' => 10,
        'precio' => 25.50,
        'activo' => true,
    ];

    $res = $this->postJson('/api/guardar-producto', $payload);

    $res->assertStatus(201)
        ->assertJsonFragment([
            'nombre' => 'Producto Nuevo',
            'sku' => 'SKU-ABC01'
        ]);

    $this->assertDatabaseHas('productos', ['sku' => 'SKU-ABC01']);
});


 Qué valida:

Que el endpoint responda con el código correcto (201 Created).

Que los datos se guarden realmente en la base de datos.

Que el formato JSON de la respuesta sea válido.

También se incluyen pruebas para:

Validación de errores (422) → cuando se envían datos incorrectos.

Lectura y eliminación → garantizando que el sistema devuelva los códigos 200 OK y 204 No Content respectivamente.

 Resumen técnico de las pruebas
Tipo de prueba	Archivo	Objetivo	Base de datos	Ejemplo de éxito
Unitaria	CategoriaModeTest	Validar estructura del modelo y relaciones	En memoria (refrescada)	Creación de categoría y relación con productos
Integración	ProductoApiTest	Probar endpoints CRUD reales	PostgreSQL (.env.test)	Creación, listado y eliminación de productos
▶Ejecución de pruebas

Para correr todas las pruebas del proyecto:

php artisan config:clear
php artisan migrate:fresh --env=test
php artisan test --env=test


Si todo está correcto, deberías ver una salida como esta:

PASS  Tests\Unit\CategoriaModeTest
PASS  Tests\Feature\ProductoApiTest