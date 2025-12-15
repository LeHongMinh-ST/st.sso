<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Aggregates;

use App\IdentityAccess\Domain\Events\PermissionWasAssignedToRole;
use App\IdentityAccess\Domain\Events\PermissionWasRemovedFromRole;
use App\IdentityAccess\Domain\Events\RoleWasCreated;
use App\IdentityAccess\Domain\ValueObjects\RoleId;

/**
 * Role aggregate root.
 * Represents a role in RBAC (Role-Based Access Control) system.
 * Manages permissions assigned to the role.
 */
final class Role
{
    private array $domainEvents = [];

    private RoleId $id;
    private string $name;
    private string $displayName;
    private ?string $description;
    /** @var array<string> Array of PermissionId strings */
    private array $permissionIds;

    /**
     * Private constructor to enforce immutability.
     *
     * @param RoleId $id
     * @param string $name
     * @param string $displayName
     * @param string|null $description
     */
    private function __construct(
        RoleId $id,
        string $name,
        string $displayName,
        ?string $description = null,
    ) {
        $this->id = $id;
        $this->name = trim($name);
        $this->displayName = trim($displayName);
        $this->description = $description ? trim($description) : null;
        $this->permissionIds = [];
    }

    /**
     * Factory method to create a new role.
     *
     * @param RoleId $id Role ID
     * @param string $name Role name (unique identifier)
     * @param string $displayName Role display name
     * @param string|null $description Role description
     * @return self
     */
    public static function create(
        RoleId $id,
        string $name,
        string $displayName,
        ?string $description = null,
    ): self {
        $role = new self($id, $name, $displayName, $description);

        $role->recordEvent(new RoleWasCreated(
            $id->toString(),
            $name,
            $displayName,
        ));

        return $role;
    }

    /**
     * Reconstruct Role from persistence (without triggering events).
     * Used when loading from database.
     *
     * @param RoleId $id
     * @param string $name
     * @param string $displayName
     * @param string|null $description
     * @param array<string> $permissionIds Array of PermissionId strings
     * @return self
     */
    public static function fromPersistence(
        RoleId $id,
        string $name,
        string $displayName,
        ?string $description = null,
        array $permissionIds = [],
    ): self {
        $role = new self($id, $name, $displayName, $description);
        $role->permissionIds = $permissionIds;

        return $role;
    }

    /**
     * Assign permission to role.
     *
     * @param string $permissionId Permission ID (UUID string)
     * @return void
     */
    public function assignPermission(string $permissionId): void
    {
        if (in_array($permissionId, $this->permissionIds, true)) {
            return; // Already assigned
        }

        $this->permissionIds[] = $permissionId;

        $this->recordEvent(new PermissionWasAssignedToRole(
            $this->id->toString(),
            $permissionId,
        ));
    }

    /**
     * Remove permission from role.
     *
     * @param string $permissionId Permission ID (UUID string)
     * @return void
     */
    public function removePermission(string $permissionId): void
    {
        $key = array_search($permissionId, $this->permissionIds, true);

        if (false === $key) {
            return; // Not assigned
        }

        unset($this->permissionIds[$key]);
        $this->permissionIds = array_values($this->permissionIds); // Re-index

        $this->recordEvent(new PermissionWasRemovedFromRole(
            $this->id->toString(),
            $permissionId,
        ));
    }

    /**
     * Check if role has permission.
     *
     * @param string $permissionId Permission ID (UUID string)
     * @return bool
     */
    public function hasPermission(string $permissionId): bool
    {
        return in_array($permissionId, $this->permissionIds, true);
    }

    /**
     * Get role ID.
     *
     * @return RoleId
     */
    public function id(): RoleId
    {
        return $this->id;
    }

    /**
     * Get role name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get role display name.
     *
     * @return string
     */
    public function displayName(): string
    {
        return $this->displayName;
    }

    /**
     * Get role description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * Get permission IDs assigned to this role.
     *
     * @return array<string>
     */
    public function permissionIds(): array
    {
        return $this->permissionIds;
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
