<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProyectoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Creamos roles y el permiso necesario
        $po = Role::create(['name' => 'product_owner', 'guard_name' => 'web']);
        Role::create(['name' => 'member', 'guard_name' => 'web']);
        
        Permission::create(['name' => 'crear_proyecto', 'guard_name' => 'web']);
        $po->givePermissionTo('crear_proyecto');

        // Estados con tus columnas reales
        DB::table('estado_proyectos')->insert([
            ['id_estado' => 1, 'estado' => 'activo'],
            ['id_estado' => 2, 'estado' => 'cerrado']
        ]);
    }

    /** TC-05: Listar proyectos */
    public function test_listar_proyectos()
    {
        /** @var User $user */
        $user = User::factory()->create();
        
        Proyecto::factory()->count(3)->create(['creado_por' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/projects');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /** TC-06: Crear proyecto (Product Owner) */
    public function test_crear_proyecto_como_jefe()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('product_owner');

        $response = $this->actingAs($user)->postJson('/api/projects', [
            'titulo' => 'Proyecto Nuevo',
            'descripcion' => 'Descripción',
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_final' => now()->addMonth()->format('Y-m-d'),
            'id_estado' => 1
        ]);

        $response->assertStatus(201);
    }

    /** TC-07: Ver detalle de proyecto */
    public function test_ver_detalle_de_proyecto()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $proyecto = Proyecto::factory()->create(['creado_por' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/projects/{$proyecto->getKey()}");

        $response->assertStatus(200);
    }

    /** TC-08: Validar campos obligatorios */
    public function test_error_si_falta_el_titulo()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('product_owner');

        $response = $this->actingAs($user)->postJson('/api/projects', [
            'descripcion' => 'Sin titulo',
            'id_estado' => 1
        ]);

        $response->assertStatus(422);
    }

    /** TC-09: Ver historial del proyecto */
    public function test_ver_historial_proyecto()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $proyecto = Proyecto::factory()->create(['creado_por' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/projects/{$proyecto->getKey()}/history");

        $response->assertStatus(200);
    }

    /** TC-15: Seguridad */
    public function test_developer_no_puede_crear_proyecto()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('member');

        $response = $this->actingAs($user)->postJson('/api/projects', [
            'titulo' => 'No permitido'
        ]);

        $response->assertStatus(403);
    }
}