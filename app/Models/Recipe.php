<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasMacros;
use App\Interfaces\Mealable;

#[Fillable(['user_id', 'name', 'calories', 'protein', 'carbs', 'fats', 'cooked_weight'])]
class Recipe extends Model Implements Mealable
{
    use HasFactory, HasMacros;

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }
}
