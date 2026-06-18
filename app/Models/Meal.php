<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['user_id', 'mealable_type', 'mealable_id', 'meal_type', 'date', 'calories', 'protein', 'carbs', 'fats', 'is_eaten'])]
#
class Meal extends Model
{
    use HasFactory;

    public function scopeDailyStats($query, $date, $mealType = null)
    {
        $query->whereDate('date', $date);
        if ($mealType) {
            $query->where('meal_type', $mealType);
        }

        return $query->selectRaw('
            COALESCE(SUM(calories), 0) as total_calories,
            COALESCE(SUM(protein), 0) as total_protein,
            COALESCE(SUM(carbs), 0) as total_carbs,
            COALESCE(SUM(fats), 0) as total_fats,

            COALESCE(SUM(CASE WHEN is_eaten = 1 THEN calories ELSE 0 END), 0) as total_eaten_calories,
            COALESCE(SUM(CASE WHEN is_eaten = 1 THEN protein ELSE 0 END), 0) as total_eaten_protein,
            COALESCE(SUM(CASE WHEN is_eaten = 1 THEN carbs ELSE 0 END), 0) as total_eaten_carbs,
            COALESCE(SUM(CASE WHEN is_eaten = 1 THEN fats ELSE 0 END), 0) as total_eaten_fats
        ');
    }
}
