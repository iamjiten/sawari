<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RentalVehicleSearchRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'pickup_location_id' => [
                'required',
                'numeric',
            ],
            'vehicle_type_id' => [
                'required',
                'exists:vehicle_types,id'
            ],
            'return_location_id' => [
                'required',
                'numeric',
            ],
            'pickup_date' => [
                'required',
                'date'
            ],
            'return_date' => [
                'required',
                'date'
            ],
            'with_driver' => [
                'required',
                'array'
            ],
        ];
    }
}
