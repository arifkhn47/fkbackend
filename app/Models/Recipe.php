<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasMacros;
use App\Interfaces\Mealable;
use Override;

#[Fillable(['user_id', 'name', 'calories', 'protein', 'carbs', 'fats', 'cooked_weight'])]
class Recipe extends Model Implements Mealable
{
    use HasFactory, HasMacros;

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function macrosPerHundred(float $quantity): array
    {
        return [
            'calories' => round($this->ingredients->sum('calories') / $quantity * 100, 2),
            'protein'  => round($this->ingredients->sum('protein') / $quantity * 100, 2),
            'carbs'    => round($this->ingredients->sum('carbs') / $quantity * 100, 2),
            'fats'     => round($this->ingredients->sum('fats') / $quantity * 100, 2),
        ];
    }
}
