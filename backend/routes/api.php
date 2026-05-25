<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
| Endpoint público para verificar se a API e o banco estão operacionais.
| Útil para orquestradores (Docker, Kubernetes) e monitoramento.
*/
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $db = 'connected';
    } catch (\Exception $e) {
        $db = 'disconnected';
    }

    return response()->json([
        'status'    => $db === 'connected' ? 'ok' : 'degraded',
        'database'  => $db,
        'timestamp' => now()->toIso8601String(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
| throttle:5,1 no login = máximo 5 tentativas por minuto por IP.
| Protege contra ataques de força bruta.
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout',  [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me',       [AuthController::class, 'me']);
    });
});

/*
|--------------------------------------------------------------------------
| Recursos protegidos
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    // Rota de status antes do apiResource para evitar conflito de wildcard
    Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus']);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('users', UserController::class)->only(['index', 'show']);
});
