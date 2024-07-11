<?php

namespace App\Http\Requests;

use App\Enums\TransactionChannelEnum;
use App\Enums\TransactionStatusEnum;
use App\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
            'parent_id' => [
                'required',
            ],
            'status' => [
                'required',
                new Enum(TransactionStatusEnum::class)
            ],
            'channel' => [
                'required',
                new Enum(TransactionChannelEnum::class)
            ],
        ];
    }

}
