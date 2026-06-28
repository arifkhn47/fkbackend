<?php

use App\Models\Food;
use App\Models\User;

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
    
});