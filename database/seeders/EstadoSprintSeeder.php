<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSprintSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estado_sprints')->insert([
            ['estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'cerrado', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
