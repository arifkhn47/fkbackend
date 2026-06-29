<?php

use App\Http\Controllers\Web\Recipes\RecipeIngredientController;
use App\Http\Controllers\Web\Recipes\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('recipes/create', [RecipeController::class, 'create'])->name('recipes.create')->middleware('auth');
Route::post('recipes', [RecipeController::class, 'store'])->name('recipes.store')->middleware('auth');
Route::get('recipe-ingredients/{recipe}/create', [RecipeIngredientController::class, 'create'])
    ->name('recipe.ingredients.create')
    ->middleware('auth');