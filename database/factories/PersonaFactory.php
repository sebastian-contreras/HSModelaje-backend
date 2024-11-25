<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class PersonaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'CUIT' => $this->faker->unique()->numerify('###########'), // 11 dígitos
            'Apellido' => $this->faker->lastName,
            'Nombre' => $this->faker->firstName,
            'Nacionalidad' => $this->faker->country,
            'Actividad' => $this->faker->word,
            'Domicilio' => $this->faker->address,
            'Email' => $this->faker->unique()->safeEmail,
            'Telefono' => $this->faker->phoneNumber,
            'Movil' => $this->faker->phoneNumber,
            'SituacionFiscal' => $this->faker->randomElement(['A', 'B', 'C']), // Ejemplo de valores
            'FNacimiento' => $this->faker->date(),
            'DNI' => $this->faker->unique()->numerify('############'), // 12 dígitos
            'Alias' => $this->faker->userName,
            'CodPostal' => $this->faker->postcode,
            'PEP' => $this->faker->randomElement(['Y', 'N']), // Ejemplo de valores
            'EstadoPersona' => $this->faker->randomElement(['A', 'I']), // Ejemplo de valores
        ];
    }
}
