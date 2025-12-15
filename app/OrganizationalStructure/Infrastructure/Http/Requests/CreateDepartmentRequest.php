<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for creating a department.
 */
final class CreateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\OrganizationalStructure\Infrastructure\Eloquent\Department::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['required', 'uuid', 'exists:faculties,uuid'],
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
            'name.required' => 'Department name is required',
            'faculty_id.required' => 'Faculty ID is required',
            'faculty_id.exists' => 'Faculty not found',
        ];
    }
}
