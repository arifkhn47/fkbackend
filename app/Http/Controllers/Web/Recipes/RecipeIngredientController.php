<?php

namespace App\Http\Controllers\Web\Recipes;

use Illuminate\Routing\Attributes\Controllers\Authorize;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Inertia\Inertia;
use Inertia\Response;

class RecipeIngredientController extends Controller
{
    #[Authorize('update', 'recipe')]
    public function create(Recipe $recipe): Response
    {
        return Inertia::render("recipes/add-ingredients");
    }
}
