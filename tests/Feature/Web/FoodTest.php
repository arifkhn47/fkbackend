<?php

use App\Models\Food;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

describe("Food Feature", function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
    });

    describe('Add Foods', function () {
        it("renders food create page", function () {
            $this->actingAs($this->user)
                ->get(route('foods.create'))
                ->assertInertia(fn($page) => $page->component('foods/create'));
        });

        it('flashes validation errors to session', function () {
            $this->actingAs($this->user)
                ->post(route('foods.store'), [])
                ->assertSessionHasErrors(['name']);
        });
        
        it('redirects guests to login', function () {
            $this->get(route('foods.create'))
                ->assertRedirect(route('login'));
        });

        test("unauthenticated user can not store food", function() {
            $this->post(route('foods.store'), [])
                ->assertRedirect(route('login'));
        });

        it("shows message after food being stored", function() {
            $food = Food::factory()->make();
            $this->actingAs($this->user)
                ->post(route('foods.store'), $food->toArray())
                ->assertInertiaFlash('toast.message', 'Food has been created!');
        });

    });

    describe("Update Food", function() {

        it("renders the edit food page", function () {
            $food = Food::factory()->create([
                'user_id' => $this->user->id
            ]);
            $this->actingAs($this->user)
                ->get(route('foods.edit', ['food' => $food]))
                ->assertInertia(fn($page) => $page->component('foods/edit'));
        });

        it("ensure that unauthorised user cannot edit food", function () {
            $food = Food::factory()->create();
            $this->actingAs($this->user)
                ->get(route('foods.edit', ['food' => $food]))
                ->assertStatus(403);
        });

        it("ensure that authenticated user can edit food", function () {
            $food = Food::factory()->create();
            $this->get(route('foods.edit', ['food' => $food]))
                ->assertRedirect(route('login'));
        });

        it("allows user to update their food", function () {
            
            $food = Food::factory()->create([
                    'user_id' => $this->user->id
            ]);

            $food->calories = 200;
            
            $this->actingAs($this->user)
                ->put(route('foods.update', ['food' => $food]), $food->toArray());

            $this->assertDatabaseHas('foods', $food->only([
                'id', 'name', 'calories', 'protein'
            ]));
        });

        it("ensure that authenticated user can update food", function () {
            $food = Food::factory()->create();
            $this->put(route('foods.update', ['food' => $food]), $food->toArray())
                ->assertRedirect(route('login'));
        });
    });

    describe("Food Search", function() {
        it('returns only foods matching the search query', function () {
            $user = User::factory()->create();

            $matchingFood = Food::factory()->create([
                'user_id' => $user->id,
                'name' => 'Chicken Breast',
            ]);

            $nonMatchingFood = Food::factory()->create([
                'user_id' => $user->id,
                'name' => 'Avocado',
            ]);

            $response = $this->actingAs($user)->get(route('foods.index', ['q' => $matchingFood->name]));

            $response->assertInertia(fn ($page) => $page
                ->component('foods/index')
                ->has('foods', 1)
                ->where('foods.0.id', $matchingFood->id)
            );
        });
        it('returns an empty array when no foods match the search query');
        it('search is case-insensitive');
        it('does not return foods belonging to other users');
        it('treats an empty search query the same as no query');
        it('returns all foods for the user when no search query is provided');
    });
    
});