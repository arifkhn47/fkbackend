<?php

use App\Http\Controllers\Api\V1\Foods\FoodController;
use Illuminate\Support\Facades\Route;

Route::post('/foods', [FoodController::class, 'store'])->name('foods.store')->middleware('auth:sanctum');
Route::put('/foods/{food}', [FoodController::class, 'update'])->name('foods.update')->middleware('auth:sanctum');
Route::get('/foods', [FoodController::class, 'index'])->name('foods.index')->middleware('auth:sanctum');
Route::get('/foods/{food}', [FoodController::class, 'show'])->name('foods.show')->middleware('auth:sanctum');