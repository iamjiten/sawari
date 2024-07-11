<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use App\Rules\Enum;
use App\Rules\MobileNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name' => [
                'max:150'
            ],
//            'email' => [
//                'max:150',
//                'email',
//                'unique:users,email,' . $this->id
//            ],
            'gender' => [
                'in:male,female,others'
            ],
            'image' => [
                'sometimes',
                'file'
            ],
            'dob' => [
                'nullable'
            ],
            'address' => [
                'nullable'
            ],
            'latitude' => [
                'nullable'
            ],
            'longitude' => [
                'nullable'
            ],
        ];
    }
}
