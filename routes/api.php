<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\SprintController;

// Rutas públicas para login y registro

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Proyectos Generales
    Route::get('/projects', [ProyectoController::class, 'index']);
    Route::post('/projects', [ProyectoController::class, 'store']);
    Route::get('/projects/{id}', [ProyectoController::class, 'show']);
    Route::get('/projects/{id}/history', [ProyectoController::class, 'history']);

    // Tareas
    Route::get('/projects/{id}/tasks', [TareaController::class, 'index']);
    Route::post('/projects/{id}/tasks', [TareaController::class, 'store']);
    Route::patch('/tasks/{id}/assign', [TareaController::class, 'assign']);
    Route::patch('/tasks/{id}/status', [TareaController::class, 'updateStatus']);
    Route::get('/tasks/me', [TareaController::class, 'myTasks']);

    // Sprints
    Route::post('/projects/{id}/sprints', [SprintController::class, 'store']);
    Route::post('/sprints/refresh', [SprintController::class, 'refreshExpirations']);
    Route::post('/projects/{id}/close-sprint', [SprintController::class, 'closeSprint']);
    Route::get('/projects/{id}/closure-summary', [SprintController::class, 'closureSummary']);
});