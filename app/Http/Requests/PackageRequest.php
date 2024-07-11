<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:150',
            ],
            'receiver_name' => [
                'required',
                'max:150',
            ],
            'receiver_nick_name' => [
//                'required',
                'max:150',
            ],
            'receiver_mobile' => [
                'required',
                'max:150',
            ],
            'receiver_id' => [
                'sometimes',
            ],
            'is_receiver_user' => [
                'sometimes',
            ],
            'package_category_id' => [
                'required',
            ],
            'package_sensible_id' => [
                'sometimes',
            ],
//            'package_category_id' => [
//                'required',
//                Rule::exists('type_settings')->where(function ($query) {
//                    $query->where('type', 'category')
//                        ->where('id', $this->input('package_category_id'));
//                })
//            ],
//            'package_sensible_id' => [
//                'sometimes',
//                Rule::exists('type_settings')->where(function ($query) {
//                    $query->where('type', 'sensible')
//                        ->where('id', $this->input('package_sensible_id'));
//                })
//            ],
            'package_size_id' => [
                'required',
                'exists:package_sizes,id'
            ],
            'sender_address' => [
                'required'
            ],
            'sender_latitude' => [
                'required'
            ],
            'sender_longitude' => [
                'required'
            ],
            'receiver_address' => [
                'required'
            ],
            'receiver_latitude' => [
                'required'
            ],
            'receiver_longitude' => [
                'required'
            ],
            'sender_receiver_distance' => [
                'required'
            ],
        ];
    }

}
