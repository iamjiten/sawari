<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Rules\BrandValueRule;
use App\Rules\Enum;
use App\Rules\ModelValueRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'key' => [
                'required',
            ],
            'value' => [
                'required'
            ],
            'status' => [
                'nullable',
                new Enum(StatusEnum::class)
            ]
        ];
    }
}
