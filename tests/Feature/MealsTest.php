<?php

use App\Models\Food;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Meals Feature', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->food = Food::factory()->create(['user_id' => $this->user->id]);
    });

    describe('Log Meal', function () {
        test('authenticated user can log a meal with a trackable food', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
                'is_eaten' => true,
                'quantity' => 1,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('meals', [
                'user_id' => $this->user->id,
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'is_eaten' => true,
            ]);
        });

        test('meal inherits calories from trackable food', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'lunch',
                'date' => now()->toDateString(),
                'is_eaten' => true,
                'quantity' => 1,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('meals', [
                'mealable_id' => $this->food->id
            ] + $this->food->calculateMacros($payload['quantity']));
        });

        test('user cannot log meal without authentication', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
                'is_eaten' => true,
            ];

            $response = $this->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(401);
        });

        test('user cannot log meal with non-existent food', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => 99999,
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
                'is_eaten' => true,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
        });

        test('user cannot log another user\'s food as meal', function () {
            $anotherUserFood = Food::factory()->create(['user_id' => $this->anotherUser->id]);

            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $anotherUserFood->id,
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
                'is_eaten' => true,
                'quantity' => 1,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(403);
        });

        test('user cannot log meal without mealable_type', function () {
            $payload = [
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
                'is_eaten' => true,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('mealable_type');
        });

        test('user cannot log meal without mealable_id', function () {
            $payload = [
                'mealable_type' => 'food',
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
                'is_eaten' => true,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('mealable_id');
        });

        test('user cannot log meal without meal_type', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'date' => now()->toDateString(),
                'is_eaten' => true,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('meal_type');
        });

        test('user cannot log meal without date', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'is_eaten' => true,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('date');
        });

        test('user cannot log meal without is_eaten', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('is_eaten');
        });

        test('user cannot log meal with invalid meal_type', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'invalid_meal',
                'date' => now()->toDateString(),
                'is_eaten' => true,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('meal_type');
        });

        test('user cannot log meal with invalid date format', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'date' => 'invalid-date',
                'is_eaten' => true,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('date');
        });

        test('user can log meal with past date', function () {
            $pastDate = now()->subDays(5)->toDateString();

            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'date' => $pastDate,
                'is_eaten' => true,
                'quantity' => 1,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('meals', [
                'user_id' => $this->user->id,
                'date' => $pastDate,
            ]);
        });

        test('user can log meal with future date', function () {
            $futureDate = now()->addDays(3)->toDateString();

            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'dinner',
                'date' => $futureDate,
                'is_eaten' => false,
                'quantity' => 1,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('meals', [
                'user_id' => $this->user->id,
                'date' => $futureDate,
            ]);
        });

        test('logged meal is automatically associated with authenticated user', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'snack',
                'date' => now()->toDateString(),
                'is_eaten' => true,
                'quantity' => 1,
            ];

            $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $meal = Meal::latest()->first();
            expect($meal->user_id)->toBe($this->user->id);
        });

        test('user can log multiple meals with same food on same day', function () {
            $date = now()->toDateString();

            $payload1 = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'date' => $date,
                'is_eaten' => true,
                'quantity' => 1,
            ];

            $payload2 = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'lunch',
                'date' => $date,
                'is_eaten' => true,
                'quantity' => 1,
            ];

            $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload1);
            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload2);

            $response->assertStatus(201);
            expect(Meal::where('user_id', $this->user->id)->count())->toBe(2);
        });

        test('user can log meal as not eaten', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
                'is_eaten' => false,
                'quantity' => 1,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('meals', [
                'user_id' => $this->user->id,
                'is_eaten' => false,
            ]);
        });

        test('user can log meal with quantity', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'lunch',
                'date' => now()->toDateString(),
                'is_eaten' => true,
                'quantity' => 2,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('meals', [
                'user_id' => $this->user->id,
                'quantity' => 2,
            ]);
        });

        test('user can\'t log meal without quantity', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'lunch',
                'date' => now()->toDateString(),
                'is_eaten' => true,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('quantity');
        });

        test('user can\'t log meal with non-numeric quantity', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'lunch',
                'date' => now()->toDateString(),
                'is_eaten' => true,
                'quantity' => 'two',
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('quantity');
        });

        test('user can\'t log meal with zero quantity', function () {
            $payload = [
                'mealable_type' => 'food',
                'mealable_id' => $this->food->id,
                'meal_type' => 'lunch',
                'date' => now()->toDateString(),
                'is_eaten' => true,
                'quantity' => 0,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('quantity');
        });
    });

    describe('Meal\'s Macros for the Day', function () {
        beforeEach(function() {
            $this->today = now()->toDateString();
        });
        test('user can get total macros for today', function () {
            $today = now()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $today,
                'is_eaten' => true,
                'calories' => 500,
                'protein' => 30,
                'carbs' => 50,
                'fats' => 20,
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $today,
                'is_eaten' => true,
                'calories' => 400,
                'protein' => 20,
                'carbs' => 40,
                'fats' => 10,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_eaten_calories', 900);
            $response->assertJsonPath('total_eaten_protein', 50);
            $response->assertJsonPath('total_eaten_carbs', 90);
            $response->assertJsonPath('total_eaten_fats', 30);

        });

        test('uneaten meal\'s macros are not counted in daily total', function () {
            $today = now()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $today,
                'is_eaten' => true,
                'calories' => 500,
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $today,
                'is_eaten' => false,
                'calories' => 600,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_eaten_calories', 500);
        });

        test('previous day meals are not counted in today total', function () {
            $yesterday = now()->subDay()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $this->today,
                'is_eaten' => true,
                'calories' => 500,
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $yesterday,
                'is_eaten' => true,
                'calories' => 800,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_eaten_calories', 500);
        });

        test('user cannot get today eaten macros without authentication', function () {

            $response = $this->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(401);
        });

        test('user can only see their own today eaten macros', function () {
            $today = now()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $today,
                'is_eaten' => true,
                'calories' => 500,
            ]);

            Meal::factory()->create([
                'user_id' => $this->anotherUser->id,
                'date' => $this->today,
                'is_eaten' => true,
                'calories' => 1000,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_eaten_calories', 500);
        });

        test('today eaten macros returns zero when no eaten meals', function () {
            $this->today = now()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $this->today,
                'is_eaten' => false,
                'calories' => 500,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_eaten_calories', 0);
        });

        test('today eaten macros returns zero for user with no meals', function () {
            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_eaten_calories', 0);
        });

        test('today eaten macros returns date', function () {
            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'date',
                'total_eaten_calories',
            ]);
        });
    });

    describe('Total Macros of Meals', function () {
        beforeEach(function() {
            $this->today = now()->toDateString();
        });
        test('user can get total macros of all their meals', function () {
            $date = now()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $date,
                'is_eaten' => true,
                'calories' => 500,
                'protein' => 30,
                'carbs' => 50,
                'fats' => 20,
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $date,
                'is_eaten' => false,
                'calories' => 400,
                'protein' => 20,
                'carbs' => 30,
                'fats' => 10,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_calories', 900);
            $response->assertJsonPath('total_protein', 50);
            $response->assertJsonPath('total_carbs', 80);
            $response->assertJsonPath('total_fats', 30);
        });

        test('total macros includes both eaten and uneaten meals', function () {
            $date = now()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $date,
                'is_eaten' => true,
                'calories' => 500,
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $date,
                'is_eaten' => false,
                'calories' => 600,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_calories', 1100);
        });

        test('user can only see their own total macros', function () {
            $date = now()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $date,
                'is_eaten' => true,
                'calories' => 500,
            ]);

            Meal::factory()->create([
                'user_id' => $this->anotherUser->id,
                'date' => $date,
                'is_eaten' => true,
                'calories' => 1000,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_calories', 500);
        });

        test('total macros returns zero for user with no meals', function () {
            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_calories', 0);
        });

        test('total macros can be filtered by date', function () {
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $today,
                'is_eaten' => true,
                'calories' => 500,
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $yesterday,
                'is_eaten' => true,
                'calories' => 800,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $yesterday]));

            $response->assertStatus(200);
            $response->assertJsonPath('total_calories', 800);
        });

        test('total macros can be filtered by date and meal_type', function () {
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $today,
                'meal_type' => 'breakfast',
                'calories' => 400,
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $today,
                'meal_type' => 'lunch',
                'calories' => 600,
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $yesterday,
                'meal_type' => 'breakfast',
                'calories' => 500,
            ]);

            $response = $this->actingAs($this->user)->getJson(
                route('api.v1.macros.index', ['date' => $today, 'meal_type' => 'breakfast'])
            );

            $response->assertStatus(200);
            $response->assertJsonPath('total_calories', 400);
        });

        test('total macros response includes macros breakdown', function () {
            Meal::factory()->create([
                'user_id' => $this->user->id,
                'calories' => 500,
                'protein' => 30,
                'carbs' => 50,
                'fats' => 15,
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.macros.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'total_calories',
                'total_protein',
                'total_carbs',
                'total_fats',
            ]);
        });
    });

    describe('Meals for the day', function () {
        beforeEach(function() {
            $this->today = now()->toDateString();
        });
        test('user can get all meals for today', function () {
            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $this->today,
                'meal_type' => 'breakfast',
            ]);

            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $this->today,
                'meal_type' => 'lunch',
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.meals.index', ['date' => $this->today]));

            $response->assertStatus(200);
            $response->assertJsonCount(2, 'data');
        });

        test('user cannot get meals for today without authentication', function () {
            $response = $this->getJson(route('api.v1.meals.index', ['date' => $this->today]));

            $response->assertStatus(401);
        });

        test('user can only see their own meals for today', function () {
            Meal::factory()->create([
                'user_id' => $this->user->id,
                'date' => $this->today,
                'meal_type' => 'breakfast',
            ]);

            Meal::factory()->create([
                'user_id' => $this->anotherUser->id,
                'date' => $this->today,
                'meal_type' => 'lunch',
            ]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.meals.index', ['date' => $this->today]));

            $response->assertStatus(200);
            expect(count($response['data']))->toBe(1);
        });
    });
});
