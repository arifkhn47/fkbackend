<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\MealableType;

class StoreMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mealable_type' => ['required', 'string', Rule::in(MealableType::getAllValues())],
            'mealable_id' => 'required|integer',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'date' => 'required|date_format:Y-m-d',
            'is_eaten' => 'required|boolean',
            'quantity' => 'required|numeric|min:1',
        ];
    }
}
