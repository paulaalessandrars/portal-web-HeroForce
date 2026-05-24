<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona:
 *   - soft_deletes: coluna deleted_at para exclusão lógica (o registro
 *     não é apagado fisicamente do banco, só marcado como deletado).
 *   - índice em status: acelera filtros por status (pendente / em andamento / concluído).
 *   - índice composto em (user_id, status): acelera filtros combinados de herói + status.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'status']);
        });
    }
};
