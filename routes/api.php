<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;

//Categorias Rutas y metodos
Route::post('/guardar-categoria', [CategoriaController::class, 'store']);
Route::get('/todas-las-categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{categoria}', [CategoriaController::class, 'show']);
Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy']);
Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']);

//Productos Rutas y metodos
Route::post('/guardar-producto', [ProductoController::class, 'store']);
Route::get('/todos-los-productos', [ProductoController::class, 'index']);
Route::get('/productos/{producto}', [ProductoController::class, 'show']);
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);
Route::put('/productos/{producto}', [ProductoController::class, 'update']);



//Traer las rutas y metodos, agregar el codigo en productos

