<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Resources\User;

use App\OrganizationalStructure\Domain\Aggregates\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User API resource.
 * Maps User aggregate to API response format.
 */
final class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id()->toString(),
            'user_name' => $user->userName()->toString(),
            'email' => (string) $user->email(),
            'full_name' => $user->fullName()->fullName(),
            'first_name' => $user->fullName()->firstName(),
            'last_name' => $user->fullName()->lastName(),
            'user_code' => $user->userCode()?->toString(),
            'phone' => $user->phoneNumber()->isNull() ? null : $user->phoneNumber()->toString(),
            'faculty_id' => $user->facultyId()?->toString(),
            'department_id' => $user->departmentId()?->toString(),
            'created_at' => $this->when($this->created_at, fn () => $this->created_at?->toIso8601String()),
            'updated_at' => $this->when($this->updated_at, fn () => $this->updated_at?->toIso8601String()),
        ];
    }
}
