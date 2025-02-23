<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Authentification
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Endpoint public pour récupérer les profils actifs
Route::get('/profiles', [ProfileController::class, 'index']);
Route::post('/profiles', [ProfileController::class, 'store'])->middleware('auth:sanctum');

// Gestion des profils
Route::match(['put', 'patch', 'delete'], '/profiles/{id}', [ProfileController::class, 'modifyOrDelete'])
    ->middleware('auth:sanctum');
