<?php

namespace App\Actions\Foods;

use App\Models\Food;

class CreateNewFood
{
    public function create(array $input): Food
    {
        return auth()->user()->foods()->create($input);
    }
}