<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreOAuthClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'redirect' => 'required|url',
            'website' => 'nullable|url',
            'description' => 'nullable|string|max:1000',
        ];
    }
}