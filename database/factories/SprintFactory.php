<?php

namespace Database\Factories;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SprintFactory extends Factory
{
    public function definition(): array
    {
        $fechaInicio = fake()->dateTimeBetween('-2 weeks', 'now');
        $fechaFinal = fake()->dateTimeBetween($fechaInicio, '+2 weeks');

        return [
            'id_proyecto' => Proyecto::factory(), 
            'id_creado_por' => User::factory(),
            'id_estado' => 1,
            'titulo' => fake()->sentence(2),
            'fecha_inicio' => $fechaInicio,
            'fecha_final' => $fechaFinal,
        ];
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_estado' => 1,
        ]);
    }

    public function cerrado(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_estado' => 2,
        ]);
    }
}