<?php

use App\Models\Food;
use App\Models\User;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('search scope filters foods by name', function () {
    $user = User::factory()->create();

    $matchingFood = Food::factory()->create([
        'user_id' => $user->id,
        'name' => 'Chicken Breast',
    ]);

    Food::factory()->create([
        'user_id' => $user->id,
        'name' => 'Avocado',
    ]);

    $results = Food::query()->search('chicken')->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($matchingFood->id);
});