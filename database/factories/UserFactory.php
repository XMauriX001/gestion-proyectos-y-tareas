<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function asProductOwner(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('product_owner');
        });
    }

    public function asProjectManager(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('project_manager');
        });
    }

    public function asMember(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('member');
        });
    }
}
