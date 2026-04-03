<?php

namespace Database\Seeders;

use App\Models\Sprint;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;

class SprintSeeder extends Seeder
{
    public function run(): void
    {
        $pm = User::where('email', 'pm@example.com')->first();
        $proyectos = Proyecto::all();

        foreach ($proyectos as $proyecto) {
            Sprint::factory(2)
                ->create([
                    'id_proyecto' => $proyecto->id_proyecto,
                    'id_creado_por' => $pm->id,
                    'id_estado' => 1, 
                ]);
        }
    }
}
