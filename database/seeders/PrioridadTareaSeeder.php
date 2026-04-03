<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioridadTareaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('prioridad_tareas')->insert([
            ['prioridad' => 'baja', 'created_at' => now(), 'updated_at' => now()],
            ['prioridad' => 'media', 'created_at' => now(), 'updated_at' => now()],
            ['prioridad' => 'alta', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}