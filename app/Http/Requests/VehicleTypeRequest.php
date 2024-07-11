<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Enums\StatusEnum;
use App\Rules\Enum;

class VehicleTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:150',
                'unique:vehicle_types,name,' . $this->id
            ],
            'weight_capacity' => [
                'required'
            ],
            'weight_unit' => [
                'sometimes'
            ],
            'icon' => [
                'nullable',
            ],
            'distance_unit' => [
                'sometimes'
            ],
            'per_distance_unit_cost' => [
                'required'
            ],
            'base_fare' => [
                'required'
            ],
            'status' => [
                'nullable',
                new Enum(StatusEnum::class)
            ],


            'extra' => [
                'nullable'
            ],
            // 'type' => [
            //     'required',
            //     'in:category,sensible'
            // ],

        ];
    }

}
