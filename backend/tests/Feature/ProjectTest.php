<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function tokenFor(User $user): string
    {
        return auth('api')->login($user);
    }

    private function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $this->tokenFor($user)];
    }

    // ─── Listagem ─────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_list_projects(): void
    {
        $user = User::factory()->create();
        Project::factory()->count(3)->create(['user_id' => $user->id]);

        $this->withHeaders($this->authHeaders($user))
            ->getJson('/api/projects')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_unauthenticated_user_cannot_list_projects(): void
    {
        $this->getJson('/api/projects')->assertStatus(401);
    }

    public function test_project_list_includes_user_relationship(): void
    {
        $user = User::factory()->create(['character' => 'Spider-Man']);
        Project::factory()->create(['user_id' => $user->id]);

        $this->withHeaders($this->authHeaders($user))
            ->getJson('/api/projects')
            ->assertStatus(200)
            ->assertJsonFragment(['character' => 'Spider-Man']);
    }

    // ─── Filtros ──────────────────────────────────────────────────────────────

    public function test_can_filter_projects_by_status(): void
    {
        $user = User::factory()->create();
        Project::factory()->count(2)->pending()->create(['user_id' => $user->id]);
        Project::factory()->count(3)->done()->create(['user_id' => $user->id]);

        $this->withHeaders($this->authHeaders($user))
            ->getJson('/api/projects?status=pendente')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_filter_projects_by_user(): void
    {
        $hero1 = User::factory()->create();
        $hero2 = User::factory()->create();
        Project::factory()->count(2)->create(['user_id' => $hero1->id]);
        Project::factory()->count(3)->create(['user_id' => $hero2->id]);

        $this->withHeaders($this->authHeaders($hero1))
            ->getJson("/api/projects?user_id={$hero1->id}")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    // ─── Criação ──────────────────────────────────────────────────────────────

    public function test_admin_can_create_project(): void
    {
        $admin = User::factory()->admin()->create();
        $hero  = User::factory()->create();

        $this->withHeaders($this->authHeaders($admin))
            ->postJson('/api/projects', [
                'name'               => 'Operação Escudo de Ferro',
                'description'        => 'Missão de alta prioridade.',
                'status'             => 'pendente',
                'user_id'            => $hero->id,
                'goal_agility'       => 80,
                'goal_enchantment'   => 70,
                'goal_efficiency'    => 90,
                'goal_excellence'    => 85,
                'goal_transparency'  => 75,
                'goal_ambition'      => 95,
            ])
            ->assertStatus(201)
            ->assertJsonFragment(['name' => 'Operação Escudo de Ferro']);

        $this->assertDatabaseHas('projects', ['name' => 'Operação Escudo de Ferro']);
    }

    public function test_hero_cannot_create_project(): void
    {
        $hero = User::factory()->hero()->create();

        $this->withHeaders($this->authHeaders($hero))
            ->postJson('/api/projects', [
                'name'    => 'Missão Não Autorizada',
                'status'  => 'pendente',
                'user_id' => $hero->id,
            ])
            ->assertStatus(403);
    }

    public function test_create_project_validates_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $this->withHeaders($this->authHeaders($admin))
            ->postJson('/api/projects', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'status', 'user_id']);
    }

    public function test_create_project_validates_status_values(): void
    {
        $admin = User::factory()->admin()->create();

        $this->withHeaders($this->authHeaders($admin))
            ->postJson('/api/projects', [
                'name'    => 'Missão Inválida',
                'status'  => 'invalido',
                'user_id' => $admin->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    // ─── Atualização ──────────────────────────────────────────────────────────

    public function test_admin_can_update_project(): void
    {
        $admin   = User::factory()->admin()->create();
        $project = Project::factory()->pending()->create(['user_id' => $admin->id]);

        $this->withHeaders($this->authHeaders($admin))
            ->putJson("/api/projects/{$project->id}", ['status' => 'concluído'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'concluído']);

        $this->assertDatabaseHas('projects', [
            'id'     => $project->id,
            'status' => 'concluído',
        ]);
    }

    public function test_hero_cannot_update_project(): void
    {
        $hero    = User::factory()->hero()->create();
        $project = Project::factory()->create(['user_id' => $hero->id]);

        $this->withHeaders($this->authHeaders($hero))
            ->putJson("/api/projects/{$project->id}", ['status' => 'concluído'])
            ->assertStatus(403);
    }

    // ─── Exclusão ─────────────────────────────────────────────────────────────

    public function test_admin_can_delete_project(): void
    {
        $admin   = User::factory()->admin()->create();
        $project = Project::factory()->create(['user_id' => $admin->id]);

        $this->withHeaders($this->authHeaders($admin))
            ->deleteJson("/api/projects/{$project->id}")
            ->assertStatus(204);

        // Com SoftDeletes o registro permanece no banco, mas deleted_at é preenchido
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_hero_cannot_delete_project(): void
    {
        $hero    = User::factory()->hero()->create();
        $project = Project::factory()->create(['user_id' => $hero->id]);

        $this->withHeaders($this->authHeaders($hero))
            ->deleteJson("/api/projects/{$project->id}")
            ->assertStatus(403);
    }

    // ─── Detalhe ──────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_project_detail(): void
    {
        $user    = User::factory()->create(['character' => 'Thor']);
        $project = Project::factory()->create(['user_id' => $user->id, 'name' => 'Missão Asgard']);

        $this->withHeaders($this->authHeaders($user))
            ->getJson("/api/projects/{$project->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'name'      => 'Missão Asgard',
                'character' => 'Thor',
            ]);
    }

    public function test_returns_404_for_nonexistent_project(): void
    {
        $user = User::factory()->create();

        $this->withHeaders($this->authHeaders($user))
            ->getJson('/api/projects/99999')
            ->assertStatus(404);
    }
}
