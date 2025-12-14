<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Entities;

use App\Enums\Status;
use App\OrganizationalStructure\Domain\Events\DepartmentWasCreated;
use App\OrganizationalStructure\Domain\Events\DepartmentWasUpdated;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;

/**
 * Department entity.
 * Represents a department in the organizational structure.
 */
final class Department
{
    private array $domainEvents = [];

    private DepartmentId $id;

    private string $name;

    private Status $status;

    private FacultyId $facultyId;

    private function __construct(
        DepartmentId $id,
        string $name,
        Status $status,
        FacultyId $facultyId,
    ) {
        $this->id = $id;
        $this->name = trim($name);
        $this->status = $status;
        $this->facultyId = $facultyId;
    }

    /**
     * Factory method to create a new department.
     *
     * @param DepartmentId $id Department ID
     * @param string $name Department name
     * @param FacultyId $facultyId Faculty ID
     * @param Status $status Status (default: Active)
     * @return self
     */
    public static function create(
        DepartmentId $id,
        string $name,
        FacultyId $facultyId,
        Status $status = Status::Active,
    ): self {
        $department = new self($id, $name, $status, $facultyId);

        $department->recordEvent(new DepartmentWasCreated(
            departmentId: $id->toString(),
            name: $name,
            facultyId: $facultyId->toString(),
        ));

        return $department;
    }

    /**
     * Reconstruct Department entity from persistence (without triggering events).
     * Used when loading from database.
     *
     * @param DepartmentId $id Department ID
     * @param string $name Department name
     * @param Status $status Status
     * @param FacultyId $facultyId Faculty ID
     * @return self
     */
    public static function fromPersistence(
        DepartmentId $id,
        string $name,
        Status $status,
        FacultyId $facultyId,
    ): self {
        // Create without triggering events - this is reconstruction from persistence
        return new self($id, $name, $status, $facultyId);
    }

    /**
     * Update department information.
     *
     * @param string $name Department name
     * @param FacultyId|null $facultyId Faculty ID (nullable)
     * @return void
     */
    public function update(string $name, ?FacultyId $facultyId = null): void
    {
        $changes = [];

        $newName = trim($name);
        if ($this->name !== $newName) {
            $changes['name'] = [
                'old' => $this->name,
                'new' => $newName,
            ];
            $this->name = $newName;
        }

        if (null !== $facultyId && !$this->facultyId->equals($facultyId)) {
            $changes['faculty_id'] = [
                'old' => $this->facultyId->toString(),
                'new' => $facultyId->toString(),
            ];
            $this->facultyId = $facultyId;
        }

        if (!empty($changes)) {
            $this->recordEvent(new DepartmentWasUpdated(
                departmentId: $this->id->toString(),
                changes: $changes,
            ));
        }
    }

    /**
     * Activate department.
     *
     * @return void
     */
    public function activate(): void
    {
        if (Status::Active === $this->status) {
            return; // Already active
        }

        $this->status = Status::Active;

        $this->recordEvent(new DepartmentWasUpdated(
            departmentId: $this->id->toString(),
            changes: [
                'status' => [
                    'old' => Status::Inactive->value,
                    'new' => Status::Active->value,
                ],
            ],
        ));
    }

    /**
     * Deactivate department.
     *
     * @return void
     */
    public function deactivate(): void
    {
        if (Status::Inactive === $this->status) {
            return; // Already inactive
        }

        $this->status = Status::Inactive;

        $this->recordEvent(new DepartmentWasUpdated(
            departmentId: $this->id->toString(),
            changes: [
                'status' => [
                    'old' => Status::Active->value,
                    'new' => Status::Inactive->value,
                ],
            ],
        ));
    }

    /**
     * Get department ID.
     *
     * @return DepartmentId
     */
    public function id(): DepartmentId
    {
        return $this->id;
    }

    /**
     * Get department name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get department status.
     *
     * @return Status
     */
    public function status(): Status
    {
        return $this->status;
    }

    /**
     * Get faculty ID.
     *
     * @return FacultyId
     */
    public function facultyId(): FacultyId
    {
        return $this->facultyId;
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
