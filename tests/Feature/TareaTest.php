<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\Sprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class TareaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // roles base
        Role::create(['name' => 'product_owner', 'guard_name' => 'web']);
        Role::create(['name' => 'member', 'guard_name' => 'web']);

        // estados de proyecto
        DB::table('estado_proyectos')->insert([
            ['id_estado' => 1, 'estado' => 'activo'],
            ['id_estado' => 2, 'estado' => 'cerrado']
        ]);
        
        // estados de sprint
        DB::table('estado_sprints')->insert([
            ['id_estado' => 1, 'estado' => 'planificado'],
            ['id_estado' => 2, 'estado' => 'activo']
        ]);
        
        // estados de tarea y sus transiciones
        DB::table('estado_tareas')->insert([
            ['id_estado' => 1, 'estado' => 'por_hacer'],
            ['id_estado' => 2, 'estado' => 'en_progreso'],
            ['id_estado' => 3, 'estado' => 'en_revision'],
            ['id_estado' => 4, 'estado' => 'completada']
        ]);
        
        // prioridades de tareas
        DB::table('prioridad_tareas')->insert([
            ['id_prioridad' => 1, 'prioridad' => 'baja'],
            ['id_prioridad' => 2, 'prioridad' => 'media'],
            ['id_prioridad' => 3, 'prioridad' => 'alta'],
        ]);
    }

    /** tc-10: crear tarea */
    public function test_crear_tarea_exitosamente()
    {
        /** @var User $user */
        $user = User::factory()->asProductOwner()->create();
        $proyecto = Proyecto::factory()->create(['creado_por' => $user->id, 'id_estado' => 1]);

        // intentamos guardar la tarea
        $response = $this->actingAs($user)->postJson("/api/projects/{$proyecto->getKey()}/tasks", [
            'titulo' => 'Tarea Nueva',
            'descripcion' => 'Hacer testing',
            'fecha_entrega' => now()->addDays(5)->format('Y-m-d'),
            'id_prioridad' => 2,
            'id_estado' => 1
        ]);

        $response->assertStatus(201);
    }

    /** tc-11: error por fecha */
    public function test_error_fecha_tarea_invalida()
    {
        /** @var User $user */
        $user = User::factory()->asProductOwner()->create();
        $proyecto = Proyecto::factory()->create([
            'creado_por' => $user->id,
            'fecha_inicio' => now()->addDays(1),
            'fecha_final' => now()->addDays(10)
        ]);

        // mandamos fecha fuera del rango del proyecto
        $response = $this->actingAs($user)->postJson("/api/projects/{$proyecto->getKey()}/tasks", [
            'titulo' => 'Tarea Mala',
            'fecha_entrega' => now()->subDays(5)->format('Y-m-d'),
            'id_prioridad' => 1
        ]);

        $response->assertStatus(400);
    }

    /** tc-12: actualizar estado */
    public function test_actualizar_estado_tarea()
    {
        /** @var User $user */
        $user = User::factory()->asMember()->create();
        
        // la tarea inicia en por_hacer
        $tarea = Tarea::factory()->create([
            'id_asignado_a' => $user->id,
            'id_estado' => 1 
        ]);

        // movemos a en_progreso
        $response = $this->actingAs($user)->patchJson("/api/tasks/{$tarea->getKey()}/status", [
            'status' => 'en_progreso'
        ]);

        $response->assertStatus(200);
    }

    /** tc-16: seguridad de estados */
    public function test_developer_no_puede_completar_tarea()
    {
        /** @var User $user */
        $user = User::factory()->asMember()->create();
        
        $tarea = Tarea::factory()->create([
            'id_asignado_a' => $user->id,
            'id_estado' => 2
        ]);

        // el dev no deberia poder marcarla como completada
        $response = $this->actingAs($user)->patchJson("/api/tasks/{$tarea->getKey()}/status", [
            'status' => 'completada'
        ]);

        $response->assertStatus(403);
    }
}