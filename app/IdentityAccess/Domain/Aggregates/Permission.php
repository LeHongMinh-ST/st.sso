<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Aggregates;

use App\IdentityAccess\Domain\Events\PermissionWasCreated;
use App\IdentityAccess\Domain\ValueObjects\PermissionId;

/**
 * Permission aggregate root.
 * Represents a permission in RBAC system.
 * Permissions are assigned to roles, and roles are assigned to users.
 */
final class Permission
{
    private array $domainEvents = [];

    private PermissionId $id;
    private string $name;
    private string $code; // Unique permission code (e.g., 'user.create', 'user.update')
    private string $displayName;
    private ?string $description;
    private ?string $permissionGroupId; // Optional: link to PermissionGroup

    /**
     * Private constructor to enforce immutability.
     *
     * @param PermissionId $id
     * @param string $name
     * @param string $code
     * @param string $displayName
     * @param string|null $description
     * @param string|null $permissionGroupId
     */
    private function __construct(
        PermissionId $id,
        string $name,
        string $code,
        string $displayName,
        ?string $description = null,
        ?string $permissionGroupId = null,
    ) {
        $this->id = $id;
        $this->name = trim($name);
        $this->code = trim($code);
        $this->displayName = trim($displayName);
        $this->description = $description ? trim($description) : null;
        $this->permissionGroupId = $permissionGroupId;
    }

    /**
     * Factory method to create a new permission.
     *
     * @param PermissionId $id Permission ID
     * @param string $name Permission name
     * @param string $code Permission code (unique identifier, e.g., 'user.create')
     * @param string $displayName Permission display name
     * @param string|null $description Permission description
     * @param string|null $permissionGroupId Permission group ID (optional)
     * @return self
     */
    public static function create(
        PermissionId $id,
        string $name,
        string $code,
        string $displayName,
        ?string $description = null,
        ?string $permissionGroupId = null,
    ): self {
        $permission = new self($id, $name, $code, $displayName, $description, $permissionGroupId);

        $permission->recordEvent(new PermissionWasCreated(
            $id->toString(),
            $code,
            $name,
        ));

        return $permission;
    }

    /**
     * Reconstruct Permission from persistence (without triggering events).
     * Used when loading from database.
     *
     * @param PermissionId $id
     * @param string $name
     * @param string $code
     * @param string $displayName
     * @param string|null $description
     * @param string|null $permissionGroupId
     * @return self
     */
    public static function fromPersistence(
        PermissionId $id,
        string $name,
        string $code,
        string $displayName,
        ?string $description = null,
        ?string $permissionGroupId = null,
    ): self {
        // Create without triggering events - this is reconstruction from persistence
        return new self($id, $name, $code, $displayName, $description, $permissionGroupId);
    }

    /**
     * Get permission ID.
     *
     * @return PermissionId
     */
    public function id(): PermissionId
    {
        return $this->id;
    }

    /**
     * Get permission name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get permission code.
     *
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }

    /**
     * Get permission display name.
     *
     * @return string
     */
    public function displayName(): string
    {
        return $this->displayName;
    }

    /**
     * Get permission description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * Get permission group ID.
     *
     * @return string|null
     */
    public function permissionGroupId(): ?string
    {
        return $this->permissionGroupId;
    }

    /**
     * Pull and clear domain events.
     *
     * @return array<object>
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    /**
     * Record a domain event.
     *
     * @param object $event Domain event
     * @return void
     */
    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
