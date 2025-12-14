<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Resources\Faculty;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Faculty collection resource.
 */
final class FacultyCollection extends ResourceCollection
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
