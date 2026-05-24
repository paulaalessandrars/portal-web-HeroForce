<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'character', 'role')->get();
        return response()->json(['data' => $users]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user->only('id', 'name', 'email', 'character', 'role')]);
    }
}
