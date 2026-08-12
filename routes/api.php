<?php

use App\Http\Controllers\PlanningController;
use Illuminate\Support\Facades\Route;

Route::post('/plannings', [PlanningController::class, 'store']);
Route::get('/plannings', [PlanningController::class, 'index']);
Route::get('/plannings/{planning}', [PlanningController::class, 'show']);