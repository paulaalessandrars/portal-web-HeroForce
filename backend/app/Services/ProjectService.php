<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Responsável por toda a lógica de negócio de projetos.
 * O controller apenas chama métodos desta classe — não conhece
 * detalhes de banco, cache ou logging.
 */
class ProjectService
{
    private const CACHE_KEY = 'projects.all';
    private const CACHE_TTL = 300; // 5 minutos

    /**
     * Retorna todos os projetos, com filtros opcionais aplicados em memória.
     * O resultado completo (sem filtros) é mantido em cache para
     * evitar consultas repetidas ao banco.
     */
    public function list(array $filters): Collection
    {
        $projects = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Project::with('user')->latest()->get();
        });

        if ($status = $filters['status'] ?? null) {
            $projects = $projects->where('status', $status);
        }

        if ($userId = $filters['user_id'] ?? null) {
            $projects = $projects->where('user_id', (int) $userId);
        }

        return $projects->values();
    }

    /**
     * Cria um projeto, invalida o cache e registra o evento.
     */
    public function create(array $data, User $performer): Project
    {
        $project = Project::create($data);
        $project->load('user');

        $this->invalidateCache();

        Log::info('project.created', [
            'project_id'   => $project->id,
            'name'         => $project->name,
            'assigned_to'  => $project->user_id,
            'performed_by' => $performer->id,
        ]);

        return $project;
    }

    /**
     * Atualiza um projeto, invalida o cache e registra as alterações.
     */
    public function update(Project $project, array $data, User $performer): Project
    {
        $project->update($data);
        $project->load('user');

        $this->invalidateCache();

        Log::info('project.updated', [
            'project_id'   => $project->id,
            'changes'      => array_keys($data),
            'performed_by' => $performer->id,
        ]);

        return $project;
    }

    /**
     * Atualiza apenas o status de um projeto.
     * Permitido a qualquer usuário autenticado (heróis podem marcar
     * suas missões como concluídas, em andamento ou pendentes).
     */
    public function updateStatus(Project $project, string $status, User $performer): Project
    {
        $project->update(['status' => $status]);
        $project->load('user');

        $this->invalidateCache();

        Log::info('project.status_updated', [
            'project_id'   => $project->id,
            'new_status'   => $status,
            'performed_by' => $performer->id,
        ]);

        return $project;
    }

    /**
     * Soft-deleta um projeto, invalida o cache e registra o evento.
     */
    public function delete(Project $project, User $performer): void
    {
        Log::info('project.deleted', [
            'project_id'   => $project->id,
            'name'         => $project->name,
            'performed_by' => $performer->id,
        ]);

        $project->delete();

        $this->invalidateCache();
    }

    /**
     * Limpa o cache da listagem de projetos.
     * Chamado sempre que um projeto é criado, editado ou removido.
     */
    private function invalidateCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
