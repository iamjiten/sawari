<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantRequest extends FormRequest
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
            'profile' => [
                'required',
            ],
            'name' => [
                'required'
            ],
            'mobile_number' => [
                'required',
                'unique:merchants,mobile_number,' . $this->id
            ],
            'email' => [
                'required',
                'email',
                'unique:merchants,email,' . $this->id
            ],
            'address' => [
                'required'
            ],
            'pan_number' => [
                'sometimes'
            ],
            'website' => [
                'sometimes'
            ],
        ];
    }
}
