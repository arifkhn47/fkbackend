<?php

namespace App\Http\Controllers\Api\V1\Meals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MacrosController extends Controller
{
    public function index(Request $request, $date, $meal_type = null)
    { 
        $result = auth()->user()
            ->meals()
            ->dailyStats($date, $meal_type)
            ->first();

        return response()->json([
            'date' => $date,
            ] + $result->toArray(), 200);
    }
}
