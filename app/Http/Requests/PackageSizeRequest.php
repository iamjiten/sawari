<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class PackageSizeRequest extends FormRequest
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
                'unique:package_sizes,name,' . $this->id
            ],
            'description' => [
                'sometimes'
            ],
            'weight' => [
                'required',
                'numeric'
            ],
            'price' => [
                'required',
                'numeric'
            ],
            'icon' => [
                'sometimes'
            ],
            'status' => [
                'sometimes',
                new Enum(StatusEnum::class)
            ],
        ];
    }

}
