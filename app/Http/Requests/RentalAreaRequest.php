<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalAreaRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'province' => [
              'required',
              'numeric',
              'in:1,2,3,4,5,6,7'
            ],
            'district' => [
                'required',
                'max:150'
            ],
            'city' => [
                'required',
                'max:150'
            ],
            'area' => [
                'required',
                'max:150'
            ],
            'status' => [
                'in:0,1'
            ],
            'extra' => [
                'array'
            ]
        ];
    }
}
