<?php

namespace App\Http\Requests;

use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class SavedReceiverRequest extends FormRequest
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
            'name' => [
                'required'
            ],
            'mobile' => [
                'required',
                new MobileNumber()
            ],
            'nick_name' => [
                'nullable'
            ],
            'address' => [
                'required'
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
