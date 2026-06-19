<?php
namespace App\Traits;

trait HasMacros
{
    public function calculateMacros(float $quantity): array
    {
        $factor = $quantity / 100;

        return collect($this->only(['calories', 'protein', 'carbs', 'fats']))
            ->map(fn($value) => round($value * $factor, 2))
            ->toArray();
    }
}