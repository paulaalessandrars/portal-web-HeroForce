<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

/**
 * Define quem pode fazer o quê com projetos.
 * O Laravel auto-descobre esta policy por convenção de nome:
 *   App\Models\Project  →  App\Policies\ProjectPolicy
 */
class ProjectPolicy
{
    /**
     * Apenas administradores podem criar projetos.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas administradores podem editar projetos.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas administradores podem excluir projetos.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->role === 'admin';
    }
}
