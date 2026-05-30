<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name'    => 'HeroForce API',
        'version' => 'v1',
        'status'  => 'online',
        'docs'    => '/api-docs/',
    ]);
});
