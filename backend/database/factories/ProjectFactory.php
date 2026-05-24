<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'               => fake()->sentence(3),
            'description'        => fake()->paragraph(),
            'status'             => fake()->randomElement(['pendente', 'em andamento', 'concluído']),
            'user_id'            => User::factory(),
            'goal_agility'       => fake()->numberBetween(0, 100),
            'goal_enchantment'   => fake()->numberBetween(0, 100),
            'goal_efficiency'    => fake()->numberBetween(0, 100),
            'goal_excellence'    => fake()->numberBetween(0, 100),
            'goal_transparency'  => fake()->numberBetween(0, 100),
            'goal_ambition'      => fake()->numberBetween(0, 100),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pendente',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'em andamento',
        ]);
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'concluído',
        ]);
    }
}
