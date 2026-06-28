<?php

namespace App\Http\Requests\Foods;

use App\Models\Food;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFoodRequest extends FormRequest
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
            'name' => [
                'sometimes', 
                'required',
                Rule::unique(Food::class)->ignore(request('food')->id)
            ],
            'calories' => 'sometimes|required|numeric|min:0',
            'protein' => 'sometimes|required|numeric|min:0',
            'carbs' => 'sometimes|required|numeric|min:0',
            'fats' => 'sometimes|required|numeric|min:0',
        ];
    }
}
