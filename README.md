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

🧪 Pruebas automatizadas (Pest / PHPUnit)

Archivo: .env.testing

APP_ENV=testing
APP_KEY=base64:TESTKEYGENERATE
APP_DEBUG=false
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
DB_FOREIGN_KEYS=true


Ejecutar:

php artisan serve