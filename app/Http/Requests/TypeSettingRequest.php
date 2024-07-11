<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class TypeSettingRequest extends FormRequest
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
                'unique:type_settings,name,' . $this->id
            ],
            'description' => [
                'sometimes'
            ],
            'icon' => [
                'nullable',
            ],
            'price' => [
                'required'
            ],
            'parent_id' => [
                'nullable'
            ],
            'type' => [
                'required',
            ],
            'status' => [
                'nullable',
                new Enum(StatusEnum::class)
            ],
        ];
    }

}
