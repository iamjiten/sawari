<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalOrderRequest extends FormRequest
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
            'vehicle_id' => [
              'required',
              'exists:vehicles,id'
            ],
            'pickup_location_id' => [
                'required',
                'numeric',
                'exists:rental_locations,id'
            ],
            'drop_off_location_id' => [
                'required',
                'numeric',
                'exists:rental_locations,id'
            ],
            'pickup_date' => [
                'required',
                'date'
            ],
            'drop_off_date' => [
                'required',
                'date'
            ]
        ];
    }
}
