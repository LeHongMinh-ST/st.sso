<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\DTOs;

/**
 * Data Transfer Object for creating a role.
 */
final class CreateRoleDTO
{
    /**
     * @param string $name Role name (unique identifier)
     * @param string $displayName Role display name
     * @param string|null $description Role description
     */
    public function __construct(
        public readonly string $name,
        public readonly string $displayName,
        public readonly ?string $description = null,
    ) {
    }

    /**
     * Create DTO from array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['display_name'] ?? '',
            $data['description'] ?? null,
        );
    }

    /**
     * Create DTO from Request.
     *
     * @param \Illuminate\Http\Request $request
     * @return self
     */
    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            $request->input('name', ''),
            $request->input('display_name', ''),
            $request->input('description'),
        );
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'display_name' => $this->displayName,
            'description' => $this->description,
        ];
    }
}
