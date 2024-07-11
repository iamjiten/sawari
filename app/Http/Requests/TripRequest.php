<?php

namespace App\Http\Requests;

use App\Enums\TripStatusEnum;
use App\Rules\CheckOrderIdExistInTripRule;
use App\Rules\Enum;
use App\Rules\OrderCancelRule;
use Illuminate\Foundation\Http\FormRequest;

class TripRequest extends FormRequest
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
                'exists:orders,id',
            ],
            'status' => [
                'not_in:0,1',
                new Enum(TripStatusEnum::class)
            ],
            'reason_id' => [
                'required_if:status,2'
            ],
            "token" => [
                'required_if:status,1'
            ]
        ];
    }
}
