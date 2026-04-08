<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_login_exitoso()
    {
        // creamos el usuario de prueba
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // intentamos el login
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    /** @test */
    public function test_login_fallido_clave_incorrecta()
    {
        // creamos usuario con clave distinta
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // mandamos clave que no es
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'clave_mala',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function test_acceso_denegado_sin_token()
    {
        // intentamos entrar a una ruta protegida sin estar logueados
        $response = $this->getJson('/api/projects');

        $response->assertStatus(401);
    }

   /** @test */
    public function test_logout_exitoso()
    {
        // preparamos el usuario
        /** @var User $user */
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // hacemos login para obtener un token real
        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        // cerramos sesion usando el token obtenido
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        $response->assertStatus(200);
    }
}