<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->words(2, true), // ejemplo: "hogar tech"
            'descripcion' => $this->faker->optional()->sentence(),
            'activa' => $this->faker->boolean(90), // 90% true
        ];
    }
}
