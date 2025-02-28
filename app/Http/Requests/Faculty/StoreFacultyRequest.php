<?php

namespace App\Http\Requests\Faculty;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:faculties,code',
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|image|max:2048'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên khoa',
            'code' => 'Mã khoa',
            'status' => 'Trạng thái',
            'logo' => 'Logo'
        ];
    } 
}