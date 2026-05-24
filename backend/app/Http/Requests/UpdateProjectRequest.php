<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Delega a decisão para ProjectPolicy::update().
     * $this->route('project') resolve o model via Route Model Binding.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('project'));
    }

    public function rules(): array
    {
        return [
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
        ];
    }

    public function failedAuthorization(): never
    {
        abort(403, 'Apenas administradores podem realizar esta ação.');
    }
}
