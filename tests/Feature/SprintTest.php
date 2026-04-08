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

        // limpiamos cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // creamos roles y el permiso que pide el request
        $po = Role::create(['name' => 'product_owner', 'guard_name' => 'web']);
        Role::create(['name' => 'member', 'guard_name' => 'web']);
        
        $permiso = Permission::create(['name' => 'editar_proyecto', 'guard_name' => 'web']);
        $po->givePermissionTo($permiso);

        // llenamos los catalogos necesarios
        DB::table('estado_proyectos')->insert([
            ['id_estado' => 1, 'estado' => 'activo'],
            ['id_estado' => 2, 'estado' => 'cerrado']
        ]);
        
        DB::table('estado_sprints')->insert([
            ['id_estado' => 1, 'estado' => 'planificado'],
            ['id_estado' => 2, 'estado' => 'activo'],
            ['id_estado' => 3, 'estado' => 'cerrado']
        ]);

        DB::table('estado_tareas')->insert([
            ['id_estado' => 1, 'estado' => 'por_hacer'],
            ['id_estado' => 4, 'estado' => 'completada']
        ]);

        DB::table('prioridad_tareas')->insert([
            ['id_prioridad' => 1, 'prioridad' => 'baja'],
            ['id_prioridad' => 2, 'prioridad' => 'media'],
            ['id_prioridad' => 3, 'prioridad' => 'alta']
        ]);
    }

    /** @test */
    public function test_crear_sprint_exitosamente()
    {
        // creamos al jefe y su proyecto
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('product_owner');

        $proyecto = Proyecto::factory()->create([
            'creado_por' => $user->id,
            'fecha_inicio' => now()->subDays(10)->format('Y-m-d'),
            'fecha_final' => now()->addDays(30)->format('Y-m-d')
        ]);

        // intentamos crear el sprint
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
        // preparamos el usuario y un proyecto corto
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('product_owner');

        $proyecto = Proyecto::factory()->create([
            'creado_por' => $user->id,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_final' => now()->addDays(5)->format('Y-m-d')
        ]);

        // mandamos fechas que se salen del rango del proyecto
        $response = $this->actingAs($user)->postJson("/api/projects/{$proyecto->getKey()}/sprints", [
            'titulo' => 'sprint invalido',
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_final' => now()->addDays(20)->format('Y-m-d')
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function test_cerrar_sprint_exitosamente()
    {
        // creamos sprint activo y una tarea para mover
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

        // ejecutamos el cierre del sprint
        $response = $this->actingAs($user)->postJson("/api/projects/{$proyecto->getKey()}/close-sprint");

        $response->assertStatus(200);
    }

    /** @test */
    public function test_refresh_expirations_cierra_sprints_viejos()
    {
        // creamos un sprint que ya expiro
        /** @var User $user */
        $user = User::factory()->create();

        Sprint::factory()->create([
            'id_estado' => 2,
            'fecha_final' => now()->subDays(1)
        ]);

        // corremos el refresh automatico
        $response = $this->actingAs($user)->postJson("/api/sprints/refresh");

        $response->assertStatus(200);
    }

    /** @test */
    public function test_member_no_puede_crear_sprint()
    {
        // intentamos crear sprint con un usuario sin permisos
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('member');

        $proyecto = Proyecto::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/projects/{$proyecto->getKey()}/sprints", [
            'titulo' => 'intento prohibido'
        ]);

        $response->assertStatus(403);
    }
}