<?php

namespace App\Http\Controllers\Api\V1\Meals;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealRequest;
use Illuminate\Database\Eloquent\Relations\Relation;

class MealController extends Controller
{
    public function index($date, $mealType = null)
    {
        $result = auth()->user()
            ->meals()
            ->where('date', $date)
            ->when($mealType, fn($q) => $q->where('meal_type', $mealType))
            ->with('mealable')
            ->get();

        return response()->json([
            'date' => $date,
            'data' => $result,
        ], 200);
    }
    
    public function store(StoreMealRequest $request)
    {
        $mealableClass = Relation::getMorphedModel($request->input('mealable_type'));
        $mealable = $mealableClass::find($request->input('mealable_id'));

        if (!$mealable) {
            return response()->json(['message' => 'Mealable item not found'], 422);
        }

        if (!auth()->user()->can('create', $mealable)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

       auth()->user()
        ->meals()
        ->create($mealable->calculateMacros($request->input('quantity')) + $request->validated());
        
        return response()->json(['message' => 'Meal stored successfully'], 201);
    }
}
