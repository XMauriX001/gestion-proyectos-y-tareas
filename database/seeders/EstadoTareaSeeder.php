<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoTareaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estado_tareas')->insert([
            ['estado' => 'por_hacer', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'en_progreso', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'en_revision', 'created_at' => now(), 'updated_at' => now()],
            ['estado' => 'completada', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
