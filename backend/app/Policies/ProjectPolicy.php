<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;

/**
 * Define quem pode fazer o quê com projetos.
 * O Laravel auto-descobre esta policy por convenção de nome:
 *   App\Models\Project  →  App\Policies\ProjectPolicy
 */
class ProjectPolicy
{
    /** Apenas administradores podem criar projetos. */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin->value;
    }

    /** Apenas administradores podem editar projetos. */
    public function update(User $user, Project $project): bool
    {
        return $user->role === UserRole::Admin->value;
    }

    /** Apenas administradores podem excluir projetos. */
    public function delete(User $user, Project $project): bool
    {
        return $user->role === UserRole::Admin->value;
    }
}
