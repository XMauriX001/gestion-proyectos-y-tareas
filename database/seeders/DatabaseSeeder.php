<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EstadoProyectoSeeder::class,
            EstadoSprintSeeder::class,
            EstadoTareaSeeder::class,
            PrioridadTareaSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            ProyectoSeeder::class,
            SprintSeeder::class,
            TareaSeeder::class,
        ]);
    }
}