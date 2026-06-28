<?php

namespace App\Http\Controllers\Web\Recipes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipes\StoreRecipeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('recipes/create');
    }

    public function store(StoreRecipeRequest $request): RedirectResponse 
    {

        auth()->user()->recipes()->create($request->validated());
        return redirect()->route('recipes.index')->with('success','Recipe created!');
    }
}
