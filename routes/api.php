<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DisponibilidadeController;
use App\Http\Controllers\AgendamentoController;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Usuários
    Route::get('/usuarios', [UserController::class, 'index']);
    Route::post('/usuarios', [UserController::class, 'store']);
    Route::get('/usuarios/{id}', [UserController::class, 'show']);
    Route::put('/usuarios/{id}', [UserController::class, 'update']);
    Route::patch('/usuarios/{id}/toggle-ativo', [UserController::class, 'toggleAtivo']);

    // Disponibilidades
    Route::get('/disponibilidades', [DisponibilidadeController::class, 'index']);
    Route::post('/disponibilidades', [DisponibilidadeController::class, 'store']);
    Route::put('/disponibilidades/{id}', [DisponibilidadeController::class, 'update']);
    Route::delete('/disponibilidades/{id}', [DisponibilidadeController::class, 'destroy']);
    Route::get('/horarios-disponiveis', [DisponibilidadeController::class, 'horariosDisponiveis']);

    // Agendamentos
    Route::get('/agendamentos', [AgendamentoController::class, 'index']);
    Route::post('/agendamentos', [AgendamentoController::class, 'store']);
    Route::get('/agendamentos/{id}', [AgendamentoController::class, 'show']);
    Route::patch('/agendamentos/{id}/cancelar', [AgendamentoController::class, 'cancelar']);
});