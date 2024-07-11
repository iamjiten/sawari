<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'image' => [
                'required',
            ],
            'vehicle_type_id' => [
                'required'
            ],
            'merchant_id' => [
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
            'production_year' => [
                'required'
            ],
            'basic_infos' => [
                'required',
                'array'
            ],
            'services' => [
                'required',
                'array'
            ],
            'detail_info' => [
                'required',
            ],
            'per_day_fare' => [
                'required',
                'numeric'
            ],
            'withDriver' => [
                'required',
                'numeric'
            ]
        ];
    }
}
