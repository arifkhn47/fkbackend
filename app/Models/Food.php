<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasMacros;
use App\Interfaces\Mealable;
use Illuminate\Database\Eloquent\Builder;

#[Fillable(['name', 'calories', 'user_id', 'protein', 'carbs', 'fats'])]
#[Table('foods')]
class Food extends Model implements Mealable
{
    use HasFactory, HasMacros;

    public function scopeSearch(Builder $query, ?string $search): void
    {
        $query->when($search, fn ($q, $search) =>
            $q->where('name', 'like', "%{$search}%")
        );
    }

}
