<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use App\Rules\Enum;
use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class
UserSignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:otps,user_id',
            ],
            'code' => [
                'required',
                Rule::exists('otps')->where(function ($query) {
                    $query->where('code', $this->code)
                        ->where('user_id', $this->user_id);
                }),
            ],
            'name' => [
                'required',
                'max:150'
            ],
            'email' => [
//                'required',
                'max:150',
                'email',
                'unique:users,email,' . $this->id
            ],
            'mobile' => [
                'required',
                'unique:users,mobile,' . $this->id,
                new MobileNumber()
            ],
            'gender' => [
                'in:male,female,others'
            ],
            'image' => [
                'sometimes',
                'file'
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
        ];
    }
}
