<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Enums\StatusEnum;
use App\Rules\Enum;

class DeliveryTypeRequest extends FormRequest
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
                'unique:delivery_types,name,' . $this->id
            ],
            'description' => [
                'nullable'
            ],

            'icon' => [
                'nullable',
            ],

            'min_day' => [
                'required'
            ],
            'max_day' => [
                'required'
            ],
            'price' => [
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
