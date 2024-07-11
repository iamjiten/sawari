<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EsewaRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "module" => [
                'required'
            ],
            "order_id" => [
                'required'
            ],
            "payment_response.productId" => [
                'required'
            ],
            "payment_response.productName" => [
                'required'
            ],
            "payment_response.totalAmount" => [
                'required'
            ],
            "payment_response.environment" => [
                'required'
            ],
            "payment_response.code" => [
                'required'
            ],
            "payment_response.merchantName" => [
                'required'
            ],
            "payment_response.message" => [
                'required',
            ],
            "payment_response.date" => [
                'required'
            ],
            "payment_response.status" => [
                'required'
            ],
            "payment_response.refId" => [
                'required'
            ]
        ];
    }
}
