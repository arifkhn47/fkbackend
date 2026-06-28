<?php

namespace App\Http\Controllers\Api\V1\Recipes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;

class RecipeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        
        auth()->user()->recipes()->create($request->only(['name', 'cooked_weight']));

        return response()->json([], 201);
    }

    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'cooked_weight' => 'required|numeric|min:1'
        ]);

        $cookedWeight = $request->input('cooked_weight');
        $recipe->update([
            'cooked_weight' => $cookedWeight,
        ] + $recipe->macrosPerHundred($cookedWeight));

        return response()->json($recipe, 201);
    }
}
