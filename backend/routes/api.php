<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Health Check — sem versionamento (endpoint de infraestrutura)
|--------------------------------------------------------------------------
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
        'version'   => 'v1',
    ]);
});

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
| Todas as rotas de negócio são versionadas em /v1.
| Isso permite lançar /v2 no futuro sem quebrar clientes existentes.
*/
Route::prefix('v1')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Autenticação
    |----------------------------------------------------------------------
    | throttle:5,1 no login = máximo 5 tentativas por minuto por IP.
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
    |----------------------------------------------------------------------
    | Recursos protegidos
    |----------------------------------------------------------------------
    */
    Route::middleware('auth:api')->group(function () {
        // Status antes do apiResource para evitar conflito de wildcard
        Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus']);
        Route::apiResource('projects', ProjectController::class);
        Route::apiResource('users', UserController::class)->only(['index', 'show']);
    });
});
