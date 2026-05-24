<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'      => 'Tony Stark',
            'email'     => 'admin@heroforce.com',
            'password'  => Hash::make('password'),
            'character' => 'Iron Man',
            'role'      => 'admin',
        ]);

        $heroes = [
            ['name' => 'Peter Parker',   'email' => 'peter@heroforce.com',   'character' => 'Spider-Man',     'role' => 'hero'],
            ['name' => 'Diana Prince',   'email' => 'diana@heroforce.com',   'character' => 'Wonder Woman',   'role' => 'hero'],
            ['name' => 'Bruce Wayne',    'email' => 'bruce@heroforce.com',   'character' => 'Batman',         'role' => 'hero'],
            ['name' => 'Steve Rogers',   'email' => 'steve@heroforce.com',   'character' => 'Captain America','role' => 'hero'],
        ];

        foreach ($heroes as $heroData) {
            User::create(array_merge($heroData, ['password' => Hash::make('password')]));
        }

        $users = User::pluck('id')->all();

        $projects = [
            [
                'name'               => 'Operação Escudo de Ferro',
                'description'        => 'Desenvolver armadura de próxima geração com IA integrada.',
                'status'             => 'em andamento',
                'user_id'            => $admin->id,
                'goal_agility'       => 85,
                'goal_enchantment'   => 90,
                'goal_efficiency'    => 70,
                'goal_excellence'    => 95,
                'goal_transparency'  => 60,
                'goal_ambition'      => 100,
            ],
            [
                'name'               => 'Projeto Teia Digital',
                'description'        => 'Sistema de monitoramento urbano com sensores bio-orgânicos.',
                'status'             => 'pendente',
                'user_id'            => $users[1] ?? $admin->id,
                'goal_agility'       => 40,
                'goal_enchantment'   => 55,
                'goal_efficiency'    => 50,
                'goal_excellence'    => 60,
                'goal_transparency'  => 70,
                'goal_ambition'      => 80,
            ],
            [
                'name'               => 'Missão Themyscira',
                'description'        => 'Estabelecer canal de comunicação entre mundos para troca cultural.',
                'status'             => 'concluído',
                'user_id'            => $users[2] ?? $admin->id,
                'goal_agility'       => 100,
                'goal_enchantment'   => 95,
                'goal_efficiency'    => 90,
                'goal_excellence'    => 88,
                'goal_transparency'  => 92,
                'goal_ambition'      => 97,
            ],
            [
                'name'               => 'Protocolo Caverna do Morcego',
                'description'        => 'Modernização do sistema de inteligência e análise forense da sede.',
                'status'             => 'em andamento',
                'user_id'            => $users[3] ?? $admin->id,
                'goal_agility'       => 60,
                'goal_enchantment'   => 45,
                'goal_efficiency'    => 75,
                'goal_excellence'    => 80,
                'goal_transparency'  => 30,
                'goal_ambition'      => 70,
            ],
            [
                'name'               => 'Iniciativa Avenger 2.0',
                'description'        => 'Recrutamento e treinamento de novos heróis para a força-tarefa.',
                'status'             => 'pendente',
                'user_id'            => $users[4] ?? $admin->id,
                'goal_agility'       => 20,
                'goal_enchantment'   => 30,
                'goal_efficiency'    => 25,
                'goal_excellence'    => 40,
                'goal_transparency'  => 50,
                'goal_ambition'      => 90,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
