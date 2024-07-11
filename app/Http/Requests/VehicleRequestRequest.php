<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequestRequest extends FormRequest
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
            'pickup_location_id' => [
                'required',
                'numeric',
                'exists:rental_locations,id',
            ],
            'drop_off_location_id' => [
                'required',
                'numeric',
                'exists:rental_locations,id'
            ],
            'pickup_off_date' => [
                'required'
            ],
            'drop_off_date' => [
                'required'
            ],
            'withDriver' => [
                'required'
            ]
        ];
    }
}
