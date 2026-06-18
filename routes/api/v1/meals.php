<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Meals\MealController;
use App\Http\Controllers\Api\V1\Meals\MacrosController;

Route::post('/meals', [MealController::class, 'store'])->name('meals.store')->middleware('auth:sanctum');

Route::get('/macros/{date}/{meal_type?}', [MacrosController::class, 'index'])->name('macros.index')->middleware('auth:sanctum');

