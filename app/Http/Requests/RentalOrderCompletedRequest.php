<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalOrderCompletedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => [
                'required'
            ]
        ];
    }
}
