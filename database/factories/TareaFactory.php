<?php

namespace Database\Factories;

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TareaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_proyecto' => Proyecto::factory(),
            'id_creado_por' => User::factory(),
            'id_asignado_a' => User::factory(),
            'id_sprint' => Sprint::factory(),
            'id_estado' => 1, // por_hacer
            'id_prioridad' => fake()->numberBetween(1, 3),
            'titulo' => fake()->sentence(4),
            'descripcion' => fake()->paragraph(2),
            'fecha_entrega' => fake()->dateTimeBetween('now', '+2 weeks'),
        ];
    }

    public function porHacer(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_estado' => 1,
        ]);
    }

    public function enProgreso(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_estado' => 2,
        ]);
    }

    public function enRevision(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_estado' => 3,
        ]);
    }

    public function completada(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_estado' => 4,
        ]);
    }

    public function conPrioridadAlta(): static
    {
        return $this->state(fn(array $attributes) => [
            'id_prioridad' => 3,
        ]);
    }
}
