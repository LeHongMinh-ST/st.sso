<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Resources\Faculty;

use App\OrganizationalStructure\Domain\Entities\Faculty;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Faculty API resource.
 * Maps Faculty entity to API response format.
 */
final class FacultyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Faculty $faculty */
        $faculty = $this->resource;

        return [
            'id' => $faculty->id()->toString(),
            'name' => $faculty->name(),
            'status' => $faculty->status()->value,
            'description' => $faculty->description(),
            'created_at' => $this->when($this->created_at, fn () => $this->created_at?->toIso8601String()),
            'updated_at' => $this->when($this->updated_at, fn () => $this->updated_at?->toIso8601String()),
        ];
    }
}
