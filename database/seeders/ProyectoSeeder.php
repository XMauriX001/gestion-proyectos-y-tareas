<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        $pm = User::where('email', 'pm@example.com')->first();

        
        Proyecto::factory(3)
            ->state(fn() => [
                'creado_por' => $pm->id,
                'id_estado' => 1, 
            ])
            ->create();
    }
}
