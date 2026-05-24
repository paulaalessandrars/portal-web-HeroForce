<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Project::with('user');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);
        }

        $projects = $query->latest()->get();

        return response()->json(['data' => $projects]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'status'             => 'required|in:pendente,em andamento,concluído',
            'user_id'            => 'required|exists:users,id',
            'goal_agility'       => 'integer|min:0|max:100',
            'goal_enchantment'   => 'integer|min:0|max:100',
            'goal_efficiency'    => 'integer|min:0|max:100',
            'goal_excellence'    => 'integer|min:0|max:100',
            'goal_transparency'  => 'integer|min:0|max:100',
            'goal_ambition'      => 'integer|min:0|max:100',
        ]);

        $project = Project::create($data);
        $project->load('user');

        return response()->json(['data' => $project], 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load('user');
        return response()->json(['data' => $project]);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name'               => 'sometimes|string|max:255',
            'description'        => 'nullable|string',
            'status'             => 'sometimes|in:pendente,em andamento,concluído',
            'user_id'            => 'sometimes|exists:users,id',
            'goal_agility'       => 'integer|min:0|max:100',
            'goal_enchantment'   => 'integer|min:0|max:100',
            'goal_efficiency'    => 'integer|min:0|max:100',
            'goal_excellence'    => 'integer|min:0|max:100',
            'goal_transparency'  => 'integer|min:0|max:100',
            'goal_ambition'      => 'integer|min:0|max:100',
        ]);

        $project->update($data);
        $project->load('user');

        return response()->json(['data' => $project]);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorizeAdmin();
        $project->delete();
        return response()->json(null, 204);
    }

    private function authorizeAdmin(): void
    {
        if (auth('api')->user()->role !== 'admin') {
            abort(403, 'Apenas administradores podem realizar esta ação.');
        }
    }
}
