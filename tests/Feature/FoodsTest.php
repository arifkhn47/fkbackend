<?php

use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Foods Feature', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
    });

    describe('Add Foods', function () {
        test('authenticated user can add a food with all required fields', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => 165,
                'protein' => 31,
                'carbs' => 0,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('foods', [
                'user_id' => $this->user->id,
                'name' => 'Chicken Breast',
                'calories' => 165,
                'protein' => 31,
            ]);
        });

        test('user cannot add food without authentication', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => 165,
                'protein' => 31,
                'carbs' => 0,
                'fats' => 3.6,
            ];

            $response = $this->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(401);
        });

        test('user cannot add food without name', function () {
            $payload = [
                'calories' => 165,
                'protein' => 31,
                'carbs' => 0,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('name');
        });

        test('user cannot add food without calories', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'protein' => 31,
                'carbs' => 0,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('calories');
        });

        test('user cannot add food without protein', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => 165,
                'carbs' => 0,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('protein');
        });

        test('user cannot add food without carbs', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => 165,
                'protein' => 31,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('carbs');
        });

        test('user cannot add food without fats', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => 165,
                'protein' => 31,
                'carbs' => 0,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('fats');
        });

        test('user cannot add food with negative calories', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => -165,
                'protein' => 31,
                'carbs' => 0,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('calories');
        });

        test('user cannot add food with negative protein', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => 165,
                'protein' => -31,
                'carbs' => 0,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('protein');
        });

        test('user cannot add food with negative carbs', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => 165,
                'protein' => 31,
                'carbs' => -5,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('carbs');
        });

        test('user cannot add food with negative fats', function () {
            $payload = [
                'name' => 'Chicken Breast',
                'calories' => 165,
                'protein' => 31,
                'carbs' => 0,
                'fats' => -3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('fats');
        });

        test('user cannot add food with empty name', function () {
            $payload = [
                'name' => '',
                'calories' => 165,
                'protein' => 31,
                'carbs' => 0,
                'fats' => 3.6,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('name');
        });

        test('user can add food with zero macros', function () {
            $payload = [
                'name' => 'Water',
                'calories' => 0,
                'protein' => 0,
                'carbs' => 0,
                'fats' => 0,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('foods', [
                'user_id' => $this->user->id,
                'name' => 'Water',
                'calories' => 0,
            ]);
        });

        test('user can add food with decimal macro values', function () {
            $payload = [
                'name' => 'Almond',
                'calories' => 579,
                'protein' => 21.55,
                'carbs' => 21.55,
                'fats' => 49.93,
            ];

            $response = $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $response->assertStatus(201);
            $this->assertDatabaseHas('foods', [
                'user_id' => $this->user->id,
                'name' => 'Almond',
                'protein' => 21.55,
            ]);
        });

        test('added food is automatically associated with authenticated user', function () {
            $payload = [
                'name' => 'Banana',
                'calories' => 89,
                'protein' => 1.1,
                'carbs' => 23,
                'fats' => 0.3,
            ];

            $this->actingAs($this->user)->postJson(route('api.v1.foods.store'), $payload);

            $food = Food::where('name', 'Banana')->first();
            expect($food->user_id)->toBe($this->user->id);
        });
    });

    describe('Update Foods', function () {

        beforeEach(function () {
            $this->food = Food::factory()->create(['user_id' => $this->user->id]);
        });

        test('user can update their own food', function () {
            $payload = [
                'name' => 'Grilled Chicken Breast',
                'calories' => 170,
                'protein' => 32,
                'carbs' => 0,
                'fats' => 3.8,
            ];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id), $payload);

            $response->assertStatus(200);
            $this->assertDatabaseHas('foods', [
                'id' => $this->food->id,
                'name' => 'Grilled Chicken Breast',
                'calories' => 170,
            ]);
        });

        test('user cannot update food without authentication', function () {
            $payload = [
                'name' => 'Updated Food',
                'calories' => 200,
                'protein' => 25,
                'carbs' => 10,
                'fats' => 5,
            ];

            $response = $this->putJson(route('api.v1.foods.update', $this->food->id ), $payload);

            $response->assertStatus(401);
        });

        test('user cannot update another user\'s food', function () {
            $payload = [
                'name' => 'Hacked Food',
                'calories' => 200,
                'protein' => 25,
                'carbs' => 10,
                'fats' => 5,
            ];

            $response = $this->actingAs($this->anotherUser)->putJson(route('api.v1.foods.update', $this->food->id   ), $payload);

            $response->assertStatus(403);
        });

        test('user can update only the name of their food', function () {
            $payload = ['name' => 'Boiled Chicken'];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(200);
            $this->assertDatabaseHas('foods', [
                'id' => $this->food->id,
                'name' => 'Boiled Chicken',
            ]);
        });

        test('user can update only calories', function () {
            $payload = ['calories' => 175];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(200);
            $this->assertDatabaseHas('foods', [
                'id' => $this->food->id,
                'calories' => 175,
            ]);
        });

        test('user can update only protein', function () {
            $payload = ['protein' => 35];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(200);
            $this->assertDatabaseHas('foods', [
                'id' => $this->food->id,
                'protein' => 35,
            ]);
        });

        test('user can update multiple fields at once', function () {
            $payload = [
                'name' => 'Roasted Chicken',
                'calories' => 180,
                'protein' => 33,
                'carbs' => 1,
            ];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(200);
            $this->assertDatabaseHas('foods', [
                'id' => $this->food->id,
                'name' => 'Roasted Chicken',
                'calories' => 180,
                'protein' => 33,
                'carbs' => 1,
            ]);
        });

        test('user cannot update food with negative calories', function () {
            $payload = ['calories' => -100];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('calories');
        });

        test('user cannot update food with negative protein', function () {
            $payload = ['protein' => -20];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('protein');
        });

        test('user cannot update food with negative carbs', function () {
            $payload = ['carbs' => -15];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('carbs');
        });

        test('user cannot update food with negative fats', function () {
            $payload = ['fats' => -5];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('fats');
        });

        test('user cannot update food with empty name', function () {
            $payload = ['name' => ''];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('name');
        });

        test('update returns updated food data', function () {
            $payload = [
                'name' => 'Updated Chicken',
                'calories' => 200,
                'protein' => 40,
                'carbs' => 2,
                'fats' => 4,
            ];

            $response = $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'id',
                'user_id',
                'name',
                'calories',
                'protein',
                'carbs',
                'fats',
            ]);
            $response->assertJsonPath('name', 'Updated Chicken');
        });

        test('user cannot update non-existent food', function () {
            $payload = [
                'name' => 'Ghost Food',
                'calories' => 100,
                'protein' => 10,
                'carbs' => 5,
                'fats' => 2,
            ];

            $response = $this->actingAs($this->user)->putJson('/api/foods/99999', $payload);

            $response->assertStatus(404);
        });

        test('user cannot delete food by updating', function () {
            $originalFood = $this->food;

            $payload = [
                'name' => $this->food->name,
                'calories' => $this->food->calories,
                'protein' => $this->food->protein,
                'carbs' => $this->food->carbs,
                'fats' => $this->food->fats,
            ];

            $this->actingAs($this->user)->putJson(route('api.v1.foods.update', $this->food->id  ), $payload);

            $this->assertDatabaseHas('foods', ['id' => $originalFood->id]);
        });
    });

    describe('User Isolation', function () {
        test('unauthorised user can\'t see foods', function () {
            $response = $this->getJson(route('api.v1.foods.index'));
            $response->assertStatus(401);
        });
        
        test('unauthorised user can\'t see other users food', function () {
            $food = Food::factory()->create(['user_id' => $this->user->id]);
            $response = $this->getJson(route('api.v1.foods.show', $food->id));
            $response->assertStatus(401);
        });

        test('user can only see their own foods', function () {
            Food::factory()->create(['user_id' => $this->user->id]);
            Food::factory()->create(['user_id' => $this->anotherUser->id]);

            $response = $this->actingAs($this->user)->getJson(route('api.v1.foods.index'));

            $response->assertStatus(200);
            $foods = $response->json();
            foreach ($foods as $food) {
                expect($food['user_id'])->toBe($this->user->id);
            }
        });

        test('another user cannot access foods through api', function () {
            $food = Food::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->anotherUser)->getJson(route('api.v1.foods.show', $food->id));

            $response->assertStatus(403);
        });
    });
});
