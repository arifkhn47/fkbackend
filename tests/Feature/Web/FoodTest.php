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

            $matchingFood = Food::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Chicken Breast',
            ]);

            $nonMatchingFood = Food::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Avocado',
            ]);

            $response = $this->actingAs($this->user)->get(route('foods.index', ['q' => $matchingFood->name]));

            $response->assertInertia(fn ($page) => $page
                ->component('foods/index')
                ->has('foods', 1)
                ->where('foods.0.id', $matchingFood->id)
            );
        });

        it('returns an empty array when no foods match the search query', function() {
            Food::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Avocado',
            ]);

            $response = $this->actingAs($this->user)->get(route('foods.index', ['q' => 'vado']));
            $response->assertInertia(fn ($page) => $page
                ->component('foods/index')
                ->has('foods', 0));

        });

        it('search is case-insensitive', function() {
            $matchingFood = Food::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Avocado',
            ]);

            $response = $this->actingAs($this->user)->get(route('foods.index', ['q' => 'Avo']));
            
            $response->assertInertia(fn ($page) => $page
                ->component('foods/index')
                ->has('foods', 1)
                ->where('foods.0.id', $matchingFood->id));
            
            $response2 = $this->actingAs($this->user)->get(route('foods.index', ['q' => 'aVo']));
            
            $response2->assertInertia(fn ($page) => $page
                ->component('foods/index')
                ->has('foods', 1)
                ->where('foods.0.id', $matchingFood->id));
        });

        it('treats an empty search query the same as no query', function () {
            Food::factory()->count(3)->create([
                'user_id' => $this->user->id,
            ]);

            $this->actingAs($this->user)->get(route('foods.index', ['q' => '']))
                ->assertInertia(fn ($page) => $page
                ->component('foods/index')
                ->has('foods', 3));
            
        });
        it('returns all foods for the user when no search query is provided', function () {
             Food::factory()->count(3)->create([
                'user_id' => $this->user->id,
            ]);

            $this->actingAs($this->user)->get(route('foods.index'))
                ->assertInertia(fn ($page) => $page
                ->component('foods/index')
                ->has('foods', 3));
        });

        it('accepts a search query within 100 characters', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->get('/foods?q=' . str_repeat('a', 100));

            $response->assertOk();
        });

        it('rejects a search query exceeding 100 characters', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->get('/foods?q=' . str_repeat('a', 101));

            $response->assertSessionHasErrors('q');
        });
        
    });
    
});