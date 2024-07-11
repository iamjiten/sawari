<?php

namespace App\Http\Requests;

use App\Enums\MoverStatusEnum;
use App\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class ChangeMoverOrderRequest extends FormRequest
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
                'exists:mover_orders,id'
            ],
            'status' => [
                'required',
                new Enum(MoverStatusEnum::class),
                'not_in:4'
            ],
            'reason_id' => [
                'required_if:status,7'
            ]
        ];
    }
}
