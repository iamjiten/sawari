<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CitizenshipRequest extends FormRequest
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
            'front_image' => [
                'sometimes',
                'file'
            ],
            'back_image' => [
                'sometimes',
                'file'
            ],
            'confirmation_image' => [
                'sometimes',
                'file'
            ],
            'citizenship_number' => [
                'required',
                'unique:citizenships,citizenship_number,' . $this->id,
            ],
            'issued_at' => [
                'required',
                'date'
            ],

        ];
    }
}
