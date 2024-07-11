<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (!$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->id(),
            ]);
        }

        return [
            'user_id' => [
                'nullable',
            ],
            'name' => [
                'required',
                'max:150',
            ],
            'nick_name' => [
                'nullable',
                'max:150'
            ],
            'description' => [
                'nullable'
            ],
            'address' => [
                'required',
                'max:255'
            ],
            'latitude' => [
                'required'
            ],
            'longitude' => [
                'required'
            ],
        ];
    }

}
