<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoverOrderRequest extends FormRequest
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
                'required',
                'exists:vehicle_types,id'
            ],
            'shifting_from_address' => [
                'required'
            ],
            'shifting_from_longitude' => [
                'required',
            ],
            'shifting_from_latitude' => [
                'required'
            ],
            'shifting_to_address' => [
                'required'
            ],
            'shifting_to_longitude' => [
                'required'
            ],
            'shifting_to_latitude' => [
                'required'
            ],
            'shifting_at' => [
                'required',
                'date'
            ],
            'no_of_rooms' => [
                'required',
                'integer'
            ],
            'galli_distance' => [
                'required',
                'numeric'
            ],
            'no_of_loader' => [
                'required',
                'integer'
            ],
            'no_of_trips' => [
                'required',
                'integer'
            ],
            'distance' => [
                'required',
                'numeric'
            ],
            'route' => [
                'required',
                'array'
            ]
        ];
    }
}
