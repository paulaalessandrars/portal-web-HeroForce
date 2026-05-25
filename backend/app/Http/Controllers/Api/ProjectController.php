<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Responsável apenas por receber a requisição HTTP, delegar ao service
 * e devolver a resposta formatada.
 * Não contém lógica de negócio — apenas orquestra.
 */
class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $this->projectService->list(
            $request->only(['status', 'user_id'])
        );

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        // Autorização já verificada no StoreProjectRequest via Policy
        $project = $this->projectService->create(
            $request->validated(),
            $request->user()
        );

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Project $project): ProjectResource
    {
        $project->load('user');

        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        // Autorização já verificada no UpdateProjectRequest via Policy
        $project = $this->projectService->update(
            $project,
            $request->validated(),
            $request->user()
        );

        return new ProjectResource($project);
    }

    public function updateStatus(Request $request, Project $project): ProjectResource
    {
        // Qualquer herói autenticado pode atualizar o status de uma missão
        $request->validate([
            'status' => ['required', 'in:pendente,em andamento,concluído'],
        ]);

        $project = $this->projectService->updateStatus(
            $project,
            $request->string('status')->value(),
            $request->user()
        );

        return new ProjectResource($project);
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        // authorize() usa o AuthorizesRequests trait + ProjectPolicy::delete()
        $this->authorize('delete', $project);

        $this->projectService->delete($project, $request->user());

        return response()->json(null, 204);
    }
}
