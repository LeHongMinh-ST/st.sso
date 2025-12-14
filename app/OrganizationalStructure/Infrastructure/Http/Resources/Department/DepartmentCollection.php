<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Resources\Department;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Department collection resource.
 */
final class DepartmentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
