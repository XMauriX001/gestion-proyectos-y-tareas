<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\Tarea;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SprintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // limpiamos los permisos de spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // preparamos roles y el permiso de edicion
        $po = Role::create(['name' => 'product_owner', 'guard_name' => 'web']);
        Role::create(['name' => 'member', 'guard_name' => 'web']);
        
        $permiso = Permission::create(['name' => 'editar_proyecto', 'guard_name' => 'web']);
        $po->givePermissionTo($permiso);

        // estados de proyecto
        DB::table('estado_proyectos')->insert([
            ['id_estado' => 1, 'estado' => 'activo'],
            ['id_estado' => 2, 'estado' => 'cerrado']
        ]);
        
        // estados para los sprints
        DB::table('estado_sprints')->insert([
            ['id_estado' => 1, 'estado' => 'planificado'],
            ['id_estado' => 2, 'estado' => 'activo'],
            ['id_estado' => 3, 'estado' => 'cerrado']
        ]);

        // estados de tarea necesarios para las transiciones
        DB::table('estado_tareas')->insert([
            ['id_estado' => 1, 'estado' => 'por_hacer'],
            ['id_estado' => 2, 'estado' => 'en_progreso'],
            ['id_estado' => 3, 'estado' => 'en_revision'],
            ['id_estado' => 4, 'estado' => 'completada']
        ]);

        // prioridades para que el factory no explote
        DB::table('prioridad_tareas')->insert([
            ['id_prioridad' => 1, 'prioridad' => 'baja'],
            ['id_prioridad' => 2, 'prioridad' => 'media'],
            ['id_prioridad' => 3, 'prioridad' => 'alta']
        ]);
    }

    /** @test */
    public function test_crear_sprint_exitosamente()
    {
        // crear sprint normal con fechas validas
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('product_owner');

        $proyecto = Proyecto::factory()->create([
            'creado_por' => $user->id,
            'fecha_inicio' => now()->subDays(10)->format('Y-m-d'),
            'fecha_final' => now()->addDays(30)->format('Y-m-d')
        ]);

        $response = $this->actingAs($user)->postJson("/api/projects/{$proyecto->getKey()}/sprints", [
            'titulo' => 'sprint 1',
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_final' => now()->addDays(7)->format('Y-m-d')
        ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function test_error_sprint_fechas_fuera_de_proyecto()
    {
        // intentar crear sprint fuera del rango del proyecto
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('product_owner');

        $proyecto = Proyecto::factory()->create([
            'creado_por' => $user->id,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_final' => now()->addDays(5)->format('Y-m-d')
        ]);

        $response = $this->actingAs($user)->postJson("/api/projects/{$proyecto->getKey()}/sprints", [
            'titulo' => 'sprint malo',
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_final' => now()->addDays(20)->format('Y-m-d')
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function test_cerrar_sprint_exitosamente()
    {
        // cierre manual de un sprint activo
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('product_owner');

        $proyecto = Proyecto::factory()->create(['creado_por' => $user->id]);
        
        $sprint = Sprint::factory()->create([
            'id_proyecto' => $proyecto->getKey(),
            'id_estado' => 2
        ]);

        Tarea::factory()->create([
            'id_proyecto' => $proyecto->getKey(),
            'id_sprint' => $sprint->getKey(),
            'id_estado' => 1
        ]);

        $response = $this->actingAs($user)->postJson("/api/sprints/{$sprint->getKey()}/close");

        $response->assertStatus(200);
    }

    /** @test */
    public function test_sprint_se_cierra_automaticamente_al_completar_tareas()
    {
        // se verifica que el sprint se cierre solo al terminar la ultima tarea
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('product_owner');

        $proyecto = Proyecto::factory()->create(['creado_por' => $user->id]);
        
        $sprint = Sprint::factory()->create([
            'id_proyecto' => $proyecto->getKey(),
            'id_estado' => 2
        ]);

        $tarea = Tarea::factory()->create([
            'id_proyecto' => $proyecto->getKey(),
            'id_sprint' => $sprint->getKey(),
            'id_asignado_a' => $user->id,
            'id_estado' => 3 // en_revision
        ]);

        // completamos la tarea y el controller deberia cerrar el sprint
        $this->actingAs($user)->patchJson("/api/tasks/{$tarea->getKey()}/status", [
            'status' => 'completada'
        ]);

        // revisamos que el estado del sprint haya pasado a 3
        $this->assertDatabaseHas('sprints', [
            'id_sprint' => $sprint->getKey(),
            'id_estado' => 3
        ]);
    }

    /** @test */
    public function test_refresh_expirations_cierra_sprints_viejos()
    {
        // validar el cierre automatico por fecha vencida
        /** @var User $user */
        $user = User::factory()->create();

        Sprint::factory()->create([
            'id_estado' => 2,
            'fecha_final' => now()->subDays(1)
        ]);

        $response = $this->actingAs($user)->postJson("/api/sprints/refresh");

        $response->assertStatus(200);
    }

    /** @test */
    public function test_member_no_puede_crear_sprint()
    {
        // verificar que un miembro no tenga permisos de jefe
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('member');

        $proyecto = Proyecto::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/projects/{$proyecto->getKey()}/sprints", [
            'titulo' => 'hack'
        ]);

        $response->assertStatus(403);
    }
}