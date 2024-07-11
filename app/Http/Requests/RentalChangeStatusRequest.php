<?php

namespace App\Http\Requests;

use App\Enums\RentalOrderStatusEnum;
use App\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class RentalChangeStatusRequest extends FormRequest
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
                'required',
                'exists:rental_orders,id'
            ],
            'status' => [
                'required',
                new Enum(RentalOrderStatusEnum::class),
            ],
            'withDriver' => [
                'required_if:status,1',
            ],
            'net_amount'=> [
                'required_if:status,1'
            ]
        ];
    }
}
