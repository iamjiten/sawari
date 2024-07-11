<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use App\Rules\Enum;
use App\Rules\MobileNumber;
use App\Rules\UserMerchantRule;
use Illuminate\Foundation\Http\FormRequest;

class
UserRequest extends FormRequest
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
                'max:150'
            ],
//            'email' => [
//                'max:150',
//                'email',
//                'unique:users,email,' . $this->id
//            ],
            'email' => [
                'sometimes'
            ],
            'mobile' => [
                'required',
                'unique:users,mobile,' . $this->id,
                new MobileNumber()
            ],
            'gender' => [
                'in:male,female,others'
            ],
            'dob' => [
                'required'
            ],
            'type' => [
                'required',
                'numeric',
                new Enum(UserTypeEnum::class)
            ],
            'status' => [
                'nullable',
                new Enum(StatusEnum::class)
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
            'image' => [
                'sometimes',
            ],
            'merchant_id' => [
                'sometimes',
                new UserMerchantRule()
            ],
        ];
    }
}
