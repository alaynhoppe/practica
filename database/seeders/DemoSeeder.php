<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::factory()
            ->count(5)                         // 5 categorías
            ->has(Producto::factory()->count(8)) // cada categoría con 8 productos
            ->create();
    }
}
