<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ─── Registro ─────────────────────────────────────────────────────────────

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Wanda Maximoff',
            'email'                 => 'wanda@heroforce.com',
            'character'             => 'Scarlet Witch',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user' => ['id', 'name', 'email', 'character', 'role'],
            ]);

        $this->assertDatabaseHas('users', [
            'email'     => 'wanda@heroforce.com',
            'character' => 'Scarlet Witch',
            'role'      => 'hero',
        ]);
    }

    public function test_registration_requires_all_fields(): void
    {
        $this->postJson('/api/v1/auth/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'character', 'password']);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'wanda@heroforce.com']);

        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Wanda Maximoff',
            'email'                 => 'wanda@heroforce.com',
            'character'             => 'Scarlet Witch',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_when_passwords_do_not_match(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Wanda Maximoff',
            'email'                 => 'wanda@heroforce.com',
            'character'             => 'Scarlet Witch',
            'password'              => 'password123',
            'password_confirmation' => 'different',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['password']);
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ])->assertStatus(200)
          ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct')]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong',
        ])->assertStatus(401)
          ->assertJsonFragment(['message' => 'Credenciais inválidas.']);
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    // ─── Me / Perfil ──────────────────────────────────────────────────────────

    public function test_authenticated_user_can_get_own_data(): void
    {
        $user  = User::factory()->create(['character' => 'Scarlet Witch']);
        $token = auth('api')->login($user);

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/v1/auth/me')
            ->assertStatus(200)
            ->assertJsonFragment([
                'email'     => $user->email,
                'character' => 'Scarlet Witch',
            ]);
    }

    public function test_unauthenticated_request_to_me_is_rejected(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user  = User::factory()->create();
        $token = auth('api')->login($user);

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJsonFragment(['message' => 'Logout realizado com sucesso.']);
    }

    public function test_unauthenticated_logout_is_rejected(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertStatus(401);
    }
}
