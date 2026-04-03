<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProyectoFactory extends Factory
{
    public function definition(): array
    {
        $fechaInicio = fake()->dateTimeBetween('-3 months', 'now');
        $fechaFinal = fake()->dateTimeBetween($fechaInicio, '+3 months');

        return [
            'creado_por' => User::factory(),
            'id_estado' => 1, // activo
            'titulo' => fake()->sentence(3),
            'descripcion' => fake()->paragraph(3),
            'fecha_inicio' => $fechaInicio,
            'fecha_final' => $fechaFinal,
        ];
    }

    public function activo(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_estado' => 1,
        ]);
    }

    public function cerrado(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_estado' => 2,
        ]);
    }
}
