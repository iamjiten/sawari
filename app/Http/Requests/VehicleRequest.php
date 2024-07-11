<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'blue_book_first_image' => [
                'sometimes',
                'file'
            ],
            'image' => [
                'sometimes',
                'file'
            ],
            'vehicle_type_id' => [
                'required'
            ],
            'brand_id' => [
                'required'
            ],
            'model_id' => [
                'required'
            ],
            'color_id' => [
                'required'
            ],
            'number_plate' => [
                'sometimes',
                'unique:vehicles,number_plate,' . $this->id,
            ],
            'production_year' => [
                'required'
            ],
        ];
    }
}
