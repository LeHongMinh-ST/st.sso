<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Entities;

use App\SharedKernel\Domain\Enums\Status;
use App\OrganizationalStructure\Domain\Events\FacultyWasCreated;
use App\OrganizationalStructure\Domain\Events\FacultyWasUpdated;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;

/**
 * Faculty entity.
 * Represents a faculty in the organizational structure.
 */
final class Faculty
{
    private array $domainEvents = [];

    private FacultyId $id;

    private string $name;

    private Status $status;

    private ?string $description;

    private function __construct(
        FacultyId $id,
        string $name,
        Status $status,
        ?string $description = null,
    ) {
        $this->id = $id;
        $this->name = trim($name);
        $this->status = $status;
        $this->description = $description ? trim($description) : null;
    }

    /**
     * Factory method to create a new faculty.
     *
     * @param FacultyId $id Faculty ID
     * @param string $name Faculty name
     * @param Status $status Status (default: Active)
     * @param string|null $description Description (nullable)
     * @return self
     */
    public static function create(
        FacultyId $id,
        string $name,
        Status $status = Status::Active,
        ?string $description = null,
    ): self {
        $faculty = new self($id, $name, $status, $description);

        $faculty->recordEvent(new FacultyWasCreated(
            facultyId: $id->toString(),
            name: $name,
        ));

        return $faculty;
    }

    /**
     * Reconstruct Faculty entity from persistence (without triggering events).
     * Used when loading from database.
     *
     * @param FacultyId $id Faculty ID
     * @param string $name Faculty name
     * @param Status $status Status
     * @param string|null $description Description (nullable)
     * @return self
     */
    public static function fromPersistence(
        FacultyId $id,
        string $name,
        Status $status,
        ?string $description = null,
    ): self {
        // Create without triggering events - this is reconstruction from persistence
        return new self($id, $name, $status, $description);
    }

    /**
     * Update faculty information.
     *
     * @param string $name Faculty name
     * @param string|null $description Description (nullable)
     * @return void
     */
    public function update(string $name, ?string $description = null): void
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

        $newDescription = $description ? trim($description) : null;
        if ($this->description !== $newDescription) {
            $changes['description'] = [
                'old' => $this->description,
                'new' => $newDescription,
            ];
            $this->description = $newDescription;
        }

        if (!empty($changes)) {
            $this->recordEvent(new FacultyWasUpdated(
                facultyId: $this->id->toString(),
                changes: $changes,
            ));
        }
    }

    /**
     * Activate faculty.
     *
     * @return void
     */
    public function activate(): void
    {
        if (Status::Active === $this->status) {
            return; // Already active
        }

        $this->status = Status::Active;

        $this->recordEvent(new FacultyWasUpdated(
            facultyId: $this->id->toString(),
            changes: [
                'status' => [
                    'old' => Status::Inactive->value,
                    'new' => Status::Active->value,
                ],
            ],
        ));
    }

    /**
     * Deactivate faculty.
     *
     * @return void
     */
    public function deactivate(): void
    {
        if (Status::Inactive === $this->status) {
            return; // Already inactive
        }

        $this->status = Status::Inactive;

        $this->recordEvent(new FacultyWasUpdated(
            facultyId: $this->id->toString(),
            changes: [
                'status' => [
                    'old' => Status::Active->value,
                    'new' => Status::Inactive->value,
                ],
            ],
        ));
    }

    /**
     * Get faculty ID.
     *
     * @return FacultyId
     */
    public function id(): FacultyId
    {
        return $this->id;
    }

    /**
     * Get faculty name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get faculty status.
     *
     * @return Status
     */
    public function status(): Status
    {
        return $this->status;
    }

    /**
     * Get faculty description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
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
