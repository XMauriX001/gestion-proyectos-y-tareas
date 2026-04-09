<?php

namespace Database\Seeders;

use App\Models\Tarea;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Database\Seeder;

class TareaSeeder extends Seeder
{
    public function run(): void
    {
        $pm = User::where('email', 'pm@example.com')->first();
        $members = User::whereHas('roles', fn ($q) => $q->where('name', 'member'))->get();
        $sprints = Sprint::all();

        foreach ($sprints as $sprint) {
            Tarea::factory(5)
                ->state(fn () => [
                    'id_proyecto' => $sprint->id_proyecto,
                    'id_sprint' => $sprint->id_sprint,
                    'id_creado_por' => $pm->id,
                    'id_asignado_a' => $members->random()->id,
                    'id_estado' => rand(1, 4),
                    'id_prioridad' => rand(1, 3),
                ])
                ->create();
        }
    }
}