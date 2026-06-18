<?php

namespace Database\Factories;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meal>
 */
class MealFactory extends Factory
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
            'mealable_type' => \App\Models\Food::class,
            'mealable_id' => \App\Models\Food::factory(),
            'meal_type' => $this->faker->randomElement(['breakfast', 'lunch', 'dinner', 'snack']),
            'date' => $this->faker->date(),
            'quantity' => $this->faker->randomFloat(2, 1, 10),
            'calories' => $this->faker->numberBetween(50, 500),
            'protein' => $this->faker->randomFloat(2, 0, 50),
            'carbs' => $this->faker->randomFloat(2, 0, 100),
            'fats' => $this->faker->randomFloat(2, 0, 30),
            'is_eaten' => $this->faker->boolean(),
        ];
    }
}
