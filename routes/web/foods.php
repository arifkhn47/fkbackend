<?php

use App\Http\Controllers\Web\Foods\FoodController;
use Illuminate\Support\Facades\Route;

Route::get('/foods', [FoodController::class, 'index'])->name('foods.index')->middleware('auth');
Route::get('/foods/create', [FoodController::class, 'create'])->name('foods.create')->middleware('auth');
Route::post('/foods', [FoodController::class, 'store'])->name('foods.store')->middleware('auth');
Route::get('/foods/{food}/edit', [FoodController::class, 'edit'])->name('foods.edit')->middleware('auth');
Route::put('/foods/{food}', [FoodController::class, 'update'])->name('foods.update')->middleware('auth');