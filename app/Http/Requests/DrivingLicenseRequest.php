<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DrivingLicenseRequest extends FormRequest
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
            'license_number' => [
                'required',
                'unique:driver_licenses,license_number,' . $this->id,
            ],
            'front_image' => [
                'sometimes',
                'file'
            ],
            'back_image' => [
                'sometimes',
                'file'
            ],
            'expired_at' => [
                'required',
            ],
        ];
    }
}
