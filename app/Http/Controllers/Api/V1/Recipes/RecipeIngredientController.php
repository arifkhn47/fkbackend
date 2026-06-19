<?php

namespace App\Http\Controllers\Api\V1\Recipes;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Food;

class RecipeIngredientController extends Controller
{
    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'ingredients' => 'required|array',
            'ingredients.*.food_id' => 'required|exists:foods,id',
            'ingredients.*.quantity' => 'required|numeric|min:1',
        ]);

        $foodIds = array_column($request->input('ingredients'), 'food_id');
        $foods   = Food::whereIn('id', $foodIds)->get()->keyBy('id');

        $ingredients = collect($request->input('ingredients'))->map(function ($item) use ($foods) {
            $food   = $foods->get($item['food_id']);
            $factor = $item['quantity'] / 100;

            return [
                'food_id'  => $food->id,
                'quantity' => $item['quantity'],
                'calories' => round($food->calories * $factor, 2),
                'protein'  => round($food->protein  * $factor, 2),
                'carbs'    => round($food->carbs    * $factor, 2),
                'fats'     => round($food->fats     * $factor, 2),
            ];
        });

        DB::transaction(function () use ($recipe, $ingredients) {
            $recipe->ingredients()->delete();
            $recipe->ingredients()->createMany($ingredients->toArray()); 
        });

        return response()->json(['message' => 'Recipe updated successfully'], 201);
    }
}
