<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Resources\Department;

use App\OrganizationalStructure\Domain\Entities\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Department API resource.
 * Maps Department entity to API response format.
 */
final class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Department $department */
        $department = $this->resource;

        return [
            'id' => $department->id()->toString(),
            'name' => $department->name(),
            'status' => $department->status()->value,
            'faculty_id' => $department->facultyId()->toString(),
            'created_at' => $this->when($this->created_at, fn () => $this->created_at?->toIso8601String()),
            'updated_at' => $this->when($this->updated_at, fn () => $this->updated_at?->toIso8601String()),
        ];
    }
}
