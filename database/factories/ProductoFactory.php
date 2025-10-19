<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'categoria_id' => Categoria::factory(),
            'nombre'       => $this->faker->unique()->words(3, true),
            'sku'          => strtoupper($this->faker->unique()->bothify('SKU-#####')),
            'stock'        => $this->faker->numberBetween(1, 500), // ✅ ENTERO
            // Si tu columna `precio` es DECIMAL/NUMERIC(10,2), usa randomFloat:
            'precio'       => $this->faker->randomFloat(2, 5, 200),
            // Si `precio` fuera INTEGER en tu migración, usa mejor:
            // 'precio'    => $this->faker->numberBetween(5, 200),
            'activo'       => $this->faker->boolean(90),
        ];
    }
}
