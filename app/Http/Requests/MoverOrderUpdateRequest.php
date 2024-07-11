<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoverOrderUpdateRequest extends FormRequest
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
            'vehicle_type_id' => [
                'exists:vehicle_types,id'
            ],
            'shifting_from_address' => [
            ],
            'shifting_from_longitude' => [
            ],
            'shifting_from_latitude' => [
            ],
            'shifting_to_address' => [
            ],
            'shifting_to_longitude' => [
            ],
            'shifting_to_latitude' => [
            ],
            'shifting_at' => [
                'date'
            ],
            'no_of_rooms' => [
                'integer'
            ],
            'galli_distance' => [
                'numeric'
            ],
            'no_of_loader' => [
                'integer'
            ],
            'no_of_trips' => [
                'integer'
            ],
            'distance' => [
                'numeric'
            ],
            'route' => [
                'array'
            ]
        ];

    }
}
