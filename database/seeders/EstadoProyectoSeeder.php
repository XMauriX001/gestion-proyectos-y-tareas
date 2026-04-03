<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoProyectoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estado_proyectos')->insert([
            ['estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'cerrado', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
