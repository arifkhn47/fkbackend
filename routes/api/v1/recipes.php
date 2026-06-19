<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Recipes\RecipeController;
use App\Http\Controllers\Api\V1\Recipes\RecipeIngredientController;

Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store')->middleware('auth:sanctum');

Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');

Route::put('/recipes/{recipe}/ingredients', [RecipeIngredientController::class, 'update'])->name('recipe_ingredients.update');