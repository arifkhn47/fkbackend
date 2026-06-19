<?php

namespace App\Enums;

enum MealableType: string
{
    case FOOD = 'food';
    case RECIPE = 'recipe';

    public static function getAllValues(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }
}