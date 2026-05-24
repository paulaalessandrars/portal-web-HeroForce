<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'description'        => $this->description,
            'status'             => $this->status,
            'user_id'            => $this->user_id,
            'goal_agility'       => $this->goal_agility,
            'goal_enchantment'   => $this->goal_enchantment,
            'goal_efficiency'    => $this->goal_efficiency,
            'goal_excellence'    => $this->goal_excellence,
            'goal_transparency'  => $this->goal_transparency,
            'goal_ambition'      => $this->goal_ambition,
            'user'               => new UserResource($this->whenLoaded('user')),
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}
