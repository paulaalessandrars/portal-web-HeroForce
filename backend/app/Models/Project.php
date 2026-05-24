<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'user_id',
        'goal_agility',
        'goal_enchantment',
        'goal_efficiency',
        'goal_excellence',
        'goal_transparency',
        'goal_ambition',
    ];

    protected function casts(): array
    {
        return [
            'goal_agility'      => 'integer',
            'goal_enchantment'  => 'integer',
            'goal_efficiency'   => 'integer',
            'goal_excellence'   => 'integer',
            'goal_transparency' => 'integer',
            'goal_ambition'     => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
