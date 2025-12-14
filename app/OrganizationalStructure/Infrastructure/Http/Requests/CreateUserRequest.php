<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for creating a user.
 */
final class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Use policies to check authorization
        return $this->user()?->can('create', \App\Models\User::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'max:255', 'unique:users,user_name'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'user_code' => ['nullable', 'string', 'max:50', 'unique:users,code'],
            'phone' => ['nullable', 'string', 'max:20'],
            'faculty_id' => ['nullable', 'uuid', 'exists:faculties,uuid'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,uuid'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_name.required' => 'Username is required',
            'user_name.unique' => 'Username already exists',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'user_code.unique' => 'User code already exists',
            'faculty_id.exists' => 'Faculty not found',
            'department_id.exists' => 'Department not found',
        ];
    }
}
