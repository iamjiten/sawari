<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_type_id' => [
                'required',
                'integer',
                'exists:delivery_types,id'
            ],
            'vehicle_type_id' => [
                'required',
                'integer',
                'exists:vehicle_types,id'
            ],
            'scheduled_at' => [
                'sometimes',
            ],
            // only id inside this array packages which has been selected
            'packages' => [
                'required',
                'array',
            ],
            'route' => [
                'required',
                'array'
            ]
        ];
    }

}
