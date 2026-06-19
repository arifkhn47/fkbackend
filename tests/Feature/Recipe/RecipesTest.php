<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Food;
use App\Models\Recipe;
use App\Models\RecipeIngredient;

uses(RefreshDatabase::class);

describe('Recipes Feature', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
    });

    describe('Create Recipe', function () {
        test("user can't create a recipe without name", function () {
            $payload = [
                'cooked_weight' => 200,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.recipes.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['name']);
        });

        test("user can create a recipe with name only", function () {
            $payload = [
                'name' => 'Test Recipe',
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.recipes.store'), $payload);

            $response->assertStatus(201);
            $response->assertJsonMissingValidationErrors([
                'calories', 
                'protein', 
                'carbs', 
                'fats', 
                'cooked_weight'
            ]);
        });

        test('allows authenticated users to create a recipe', function () {
            $payload = [
                'name' => 'Test Recipe',
                'cooked_weight' => 200,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.recipes.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('recipes', [
                'name' => 'Test Recipe',
                'user_id' => $this->user->id,
            ]);
        });

        test('does not allow unauthenticated users to create a recipe', function () {
            $payload = [
                'name' => 'Test Recipe',
                'calories' => 250,
                'protein' => 10,
                'carbs' => 30,
                'fats' => 5,
            ];

            $response = $this->postJson(route('api.v1.recipes.store'), $payload);

            $response->assertStatus(401);
        });
    });

    describe("Recipe's ingredients", function () {
        beforeEach(function () {
            $this->recipe = $this->user->recipes()->create([
                'name' => 'Test Recipe',
            ]);
        });
        test("user can't add ingredients to a recipe without ingredients", function () {
            $payload = [];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipe_ingredients.update', $this->recipe->id), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['ingredients']);
        });

        test("user can't add ingredients to a recipe with invalid ingredients", function () {
            $payload = [
                'ingredients' => 'invalid ingredients',
            ];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipe_ingredients.update', $this->recipe->id), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['ingredients']);
        });

        test("user can't add ingredients to a recipe without food_id", function () {
            $payload = [
                'ingredients' => [
                    [
                        'quantity' => 100,
                    ],
                ],
            ];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipe_ingredients.update', $this->recipe->id), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['ingredients.0.food_id']);
        });

        test("user can't add ingredients to a recipe with non-existing food_id", function () {
            $payload = [
                'ingredients' => [
                    [
                        'food_id' => 999,
                        'quantity' => 100,
                    ],
                ],
            ];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipe_ingredients.update', $this->recipe->id), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['ingredients.0.food_id']);
        });

        test("user can't add ingredients to a recipe without quantity", function () {
            $food = Food::factory()->create();
            $payload = [
                'ingredients' => [
                    [
                        'food_id' => $food->id,
                    ],
                ],
            ];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipe_ingredients.update', $this->recipe->id), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['ingredients.0.quantity']);
        });

        test("user can't add ingredients to a recipe with invalid quantity", function () {
            $food = Food::factory()->create();
            $payload = [
                'ingredients' => [
                    [
                        'food_id' => $food->id,
                        'quantity' => -100,
                    ],
                ],
            ];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipe_ingredients.update', $this->recipe->id), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['ingredients.0.quantity']);
        });

        test("user can add ingredients to a recipe", function () {
            $payload = [
                'ingredients' => Food::factory()->count(3)->create()->map(function ($food) {
                    return [
                        'food_id' => $food->id,
                        'quantity' => 100,
                    ]; 
                })->toArray()
            ];
            
            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipe_ingredients.update', $this->recipe->id), $payload);
            $response->assertStatus(201);
        });
    });

    describe("Recipe can be mealable", function () {
        beforeEach(function () {
            $this->recipe = Recipe::factory()->create([
                'user_id' => $this->user->id
            ]);
        });
        test("user can add recipe as mealable to meals", function () {
            $payload = [
                'mealable_type' => 'recipe',
                'mealable_id' => $this->recipe->id,
                'meal_type' => 'breakfast',
                'date' => now()->toDateString(),
                'is_eaten' => true,
                'quantity' => 50,
            ];
            $response = $this->actingAs($this->user)->postJson(route('api.v1.meals.store'), $payload);
            $response->assertStatus(201);
            $this->assertDatabaseHas('meals', [
                'mealable_id' => $this->recipe->id,
            ] + $this->recipe->calculateMacros($payload['quantity']));
        });
    });

    describe("Recipe macros", function() {
        beforeEach(function() {
            $this->recipe = $this->user->recipes()->create([
                'name' => "My Recipe"
            ]);
        });

        test("user cannot store per 100gm macros of recipe without cooked_weight", function() {
            $payload = [];
            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipes.update', $this->recipe->id), $payload);
            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['cooked_weight']);
        });

        test("user cannot store per 100gm macros of recipe invalid cooked_weight", function() {
            $payload = [
                'cooked_weight' => 'invalid'
            ];
            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipes.update', $this->recipe->id), $payload);
            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['cooked_weight']);
        });

        test("user cannot store per 100gm macros of recipe Zero cooked_weight", function() {
            $payload = [
                'cooked_weight' => 0
            ];
            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipes.update', $this->recipe->id), $payload);
            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['cooked_weight']);
        });

        test("user can store per 100gm macros of recipe valid cooked_weight", function() {
            RecipeIngredient::factory()->create([
                'recipe_id' => $this->recipe->id,
                'quantity' => 70,
                'calories' => 231,
                'protein' => 36.4,
                'carbs' => 26.6,
                'fats' => 0.7,
            ]);
            RecipeIngredient::factory()->create([
                'recipe_id' => $this->recipe->id,
                'quantity' => 100,
                'calories' => 318,
                'protein' => 21.2,
                'carbs' => 3.5,
                'fats' => 24.7,
            ]);
            RecipeIngredient::factory()->create([
                'recipe_id' => $this->recipe->id,
                'quantity' => 10,
                'calories' => 38.7,
                'protein' => 2.2,
                'carbs' => 5.8,
                'fats' => 0.7,
            ]);

            $payload = [
                'cooked_weight' => 400
            ];
            $response = $this->actingAs($this->user)->putJson(route('api.v1.recipes.update', $this->recipe->id), $payload);
            $response->assertStatus(201);

            $this->assertDatabaseHas('recipes', [
                'id' => $this->recipe->id,
                'cooked_weight' => $payload['cooked_weight'],
                'calories' => 146.93,
                'protein' => 14.95,
                'carbs' => 8.98,
                'fats' => 6.53
            ]);
        });
        
    });
});