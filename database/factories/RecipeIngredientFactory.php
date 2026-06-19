<?php

namespace Database\Factories;

use App\Models\RecipeIngredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecipeIngredient>
 */
class RecipeIngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipe_id' => \App\Models\Recipe::factory(),
            'food_id' => \App\Models\Food::factory(),
            'calories' => $this->faker->numberBetween(50, 500),
            'protein' => $this->faker->randomFloat(2, 0, 50),
            'carbs' => $this->faker->randomFloat(2, 0, 100),
            'fats' => $this->faker->randomFloat(2, 0, 30),
            'quantity' => $this->faker->randomFloat(2, 1, 10),
        ];
    }
}
