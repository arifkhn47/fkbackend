<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->word(),
            'calories' => $this->faker->numberBetween(50, 500),
            'protein' => $this->faker->randomFloat(2, 0, 50),
            'carbs' => $this->faker->randomFloat(2, 0, 100),
            'fats' => $this->faker->randomFloat(2, 0, 30),
            'cooked_weight' => $this->faker->randomFloat(2, 1, 10),
        ];
    }
}
