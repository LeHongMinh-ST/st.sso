<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::guard('api')->user();
        return $user && Role::SuperAdmin === $user->role;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_name' => 'required|max:255|unique:users,user_name',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|max:255',
            'role' => 'required',
            'code' => 'nullable|max:255|unique:users,code',
            'department_id' => 'nullable',
            'faculty_id' => 'nullable',
            'password' => 'required|min:8|max:255',
        ];
    }
}
