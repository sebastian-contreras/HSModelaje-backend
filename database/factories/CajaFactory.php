<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Caja>
 */
class CajaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'NumeroCaja' => $this->faker->numberBetween(1, 100), // Número de caja aleatorio
            'Tamaño' => $this->faker->randomElement(['S', 'M', 'L', 'XL','XXL','XXXL']), // Tamaño aleatorio
            'Ubicacion' => $this->faker->address, // Dirección aleatoria
            'Fila' => $this->faker->numberBetween(1, 20), // Fila aleatoria
            'Columna' => $this->faker->numberBetween(1, 20), // Columna aleatoria
            'Observaciones' => $this->faker->sentence, // Observaciones aleatorias
            'EstadoCaja' => $this->faker->randomElement(['A', 'I']), // Estado aleatorio (A: Activo, I: Inactivo)
        ];
    }
}
