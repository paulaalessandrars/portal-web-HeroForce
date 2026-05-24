<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Delega a decisão de autorização para ProjectPolicy::create().
     * Se o usuário não for admin, o Laravel retorna 403 automaticamente.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Project::class);
    }

    public function rules(): array
    {
        return [
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
        ];
    }

    public function failedAuthorization(): never
    {
        abort(403, 'Apenas administradores podem realizar esta ação.');
    }
}
