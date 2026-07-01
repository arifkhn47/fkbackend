<?php

namespace App\Http\Controllers\Web\Foods;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foods\StoreFoodRequest;
use App\Actions\Foods\CreateNewFood;
use App\Http\Requests\Foods\UpdateFoodRequest;
use App\Models\Food;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

class FoodController extends Controller
{
    public function index(): Response
    {
        $validated = request()->validate([
            'q' => 'nullable|string|max:100',
        ]);

        $search = $validated['q'] ?? null;
        $foods = auth()->user()->foods()
            ->search($search)
            ->get();
        return Inertia::render('foods/index', [
            'foods' => $foods
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('foods/create');
    }

    public function store(StoreFoodRequest $request, CreateNewFood $createNewFood)
    {
        $createNewFood->create($request->validated());
        Inertia::flash('toast', ['type' => 'success', 'message' => __('Food has been created!')]);
        return to_route('foods.index');
    }

    #[Authorize('update', 'food')]
    public function edit(Food $food): Response
    {
        return Inertia::render('foods/edit', [
            'food' => $food
        ]);
    }

    #[Authorize('update', 'food')]
    public function update(UpdateFoodRequest $request, Food $food): RedirectResponse
    {
        $food->update($request->validated());
        Inertia::flash('toast', ['type' => 'success', 'message' => __('Food has been created!')]);
        return to_route('foods.index');
    }
}
