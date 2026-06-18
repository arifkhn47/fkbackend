<?php

namespace App\Http\Controllers\Foods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foods\StoreFoodRequest;
use App\Actions\Foods\CreateNewFood;

class FoodController extends Controller
{
    public function store(StoreFoodRequest $request, CreateNewFood $createNewFood)
    {
        $food = $createNewFood->create($request->validated());
        return response()->json($food, 201);
    }
}
