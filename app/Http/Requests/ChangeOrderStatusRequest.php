<?php

namespace App\Http\Requests;

use App\Rules\OrderCancelRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeOrderStatusRequest extends FormRequest
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
            'order_id' => [
                'required'
            ],
            'status' => [
                'required',
                'in:1,3,4,5,7'
            ],
            "token" => [
                'required_if:status,4'
            ],
//            "receiver_id" => [
//                'required_if:status,4,5'
//            ]
        ];
    }
}
