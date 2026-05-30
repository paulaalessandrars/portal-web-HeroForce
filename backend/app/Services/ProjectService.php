<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
     * Retorna projetos paginados conforme o papel do usuário:
     * - Admin → todos os projetos com filtros opcionais
     * - Herói → somente os projetos atribuídos a ele
     *
     * @return LengthAwarePaginator
     */
    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 12), 50); // máx 50 por página

        $query = Project::with('user')->latest();

        if ($user->role === UserRole::Admin->value) {
            // Admin filtra por herói se quiser
            if ($userId = $filters['user_id'] ?? null) {
                $query->where('user_id', (int) $userId);
            }
        } else {
            // Herói só enxerga os próprios projetos
            $query->where('user_id', $user->id);
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($search = $filters['search'] ?? null) {
            $query->where('name', 'ilike', "%{$search}%");
        }

        return $query->paginate($perPage);
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
