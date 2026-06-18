<?php

namespace App\Http\Controllers\Api\V1\Foods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foods\StoreFoodRequest;
use App\Actions\Foods\CreateNewFood;
use App\Models\Food;  
use App\Http\Requests\Foods\UpdateFoodRequest;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::where('user_id', auth()->id())->get();
        return response()->json($foods, 200);
    }
    public function show(Food $food)
    {
        if ($food->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($food, 200);
    }

    public function store(StoreFoodRequest $request, CreateNewFood $createNewFood)
    {
        $food = $createNewFood->create($request->validated());
        return response()->json($food, 201);
    }

    public function update(UpdateFoodRequest $request, Food $food)
    {
        if ($food->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $food->update($request->validated());
        return response()->json($food, 200);
    }
}