<?php

namespace Database\Factories;

use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Food>
 */
class FoodFactory extends Factory
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
        ];
    }
}
