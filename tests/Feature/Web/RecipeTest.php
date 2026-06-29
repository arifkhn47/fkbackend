<?php

use App\Models\Recipe;
use App\Models\User;

describe("Recipe Feature", function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
    });

    describe("Create Recipe", function () {
        test("renders recipe create page", function () {
            $this->actingAs($this->user)
                ->get(route('recipes.create'))
                ->assertInertia(fn($page) => $page->component('recipes/create'));
        });

        test("renders recipe create page to authenticated users only", function () {
            $this->get(route('recipes.create'))
                ->assertRedirect(route('login'));
        });

        test("only authenticated use can store a recipe", function () {
            $this->post(route('recipes.store'), [])
                ->assertRedirect(route('login'));
        });

        it('flashes validation errors to session', function () {
            $this->actingAs($this->user)
                ->post(route('recipes.store'), [])
                ->assertSessionHasErrors(['name']);
        });
    });

    describe('Recipe Ingredients', function () {
        beforeEach(function () {
            $this->user = User::factory()->create();
            $this->recipe = Recipe::factory()->create(['user_id'=> $this->user->id]);
        });

        it('redirect user to login page if not logged in', function() {
            $this->get(route('recipe.ingredients.create', ['recipe' => $this->recipe->id]))
                ->assertRedirect(route('login'));

        });

        it('renders recipe ingredients component', function() {
            $this->actingAs($this->user)
                ->get(route('recipe.ingredients.create', ['recipe' => $this->recipe->id]))
                ->assertInertia(fn($page) => $page->component('recipes/add-ingredients'));
        });

        it('ensures only recipe owner access add ingredient page', function() {
            $this->actingAs(User::factory()->create())
                ->get(route('recipe.ingredients.create', ['recipe' => $this->recipe->id]))
                ->assertStatus(403);

        });

    });
});