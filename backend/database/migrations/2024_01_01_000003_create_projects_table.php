<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['pendente', 'em andamento', 'concluído'])->default('pendente');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('goal_agility')->default(0);
            $table->unsignedTinyInteger('goal_enchantment')->default(0);
            $table->unsignedTinyInteger('goal_efficiency')->default(0);
            $table->unsignedTinyInteger('goal_excellence')->default(0);
            $table->unsignedTinyInteger('goal_transparency')->default(0);
            $table->unsignedTinyInteger('goal_ambition')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
