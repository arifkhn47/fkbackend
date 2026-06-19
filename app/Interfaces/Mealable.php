<?php

namespace App\Interfaces;

interface Mealable
{
    public function calculateMacros(float $quantity): array;
}
