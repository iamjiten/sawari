<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'max:300',
                'unique:blogs,title,' . $this->id
            ],
            'icon' => [
                'required',
            ],
            'body' => [
                'required'
            ],
        ];
    }

}
