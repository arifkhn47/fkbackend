<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[WithoutTimestamps]
#[Fillable('recipe_id', 'food_id', 'quantity', 'calories', 'protein', 'carbs', 'fats')]
class RecipeIngredient extends Model
{
    use HasFactory;
}
