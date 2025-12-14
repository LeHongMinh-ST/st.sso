<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Aggregates;

use App\OrganizationalStructure\Domain\Events\UserWasAssignedToDepartment;
use App\OrganizationalStructure\Domain\Events\UserWasAssignedToFaculty;
use App\OrganizationalStructure\Domain\Events\UserWasCreated;
use App\OrganizationalStructure\Domain\Events\UserWasUpdated;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Domain\ValueObjects\FullName;
use App\OrganizationalStructure\Domain\ValueObjects\PhoneNumber;
use App\OrganizationalStructure\Domain\ValueObjects\UserCode;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\OrganizationalStructure\Domain\ValueObjects\UserName;
use App\SharedKernel\Domain\ValueObjects\Email;

/**
 * User aggregate root.
 * Represents a user profile (not authentication).
 */
final class User
{
    private array $domainEvents = [];

    private UserId $id;

    private UserName $userName;

    private FullName $fullName;

    private Email $email;

    private ?UserCode $userCode;

    private PhoneNumber $phoneNumber;

    private ?FacultyId $facultyId;

    private ?DepartmentId $departmentId;

    private function __construct(
        UserId $id,
        UserName $userName,
        FullName $fullName,
        Email $email,
        ?UserCode $userCode = null,
        ?PhoneNumber $phoneNumber = null,
        ?FacultyId $facultyId = null,
        ?DepartmentId $departmentId = null,
    ) {
        $this->id = $id;
        $this->userName = $userName;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->userCode = $userCode;
        $this->phoneNumber = $phoneNumber ?? PhoneNumber::fromString(null);
        $this->facultyId = $facultyId;
        $this->departmentId = $departmentId;
    }

    /**
     * Factory method to create a new user.
     *
     * @param UserId $id User ID
     * @param UserName $userName Username
     * @param FullName $fullName Full name
     * @param Email $email Email address
     * @param UserCode|null $userCode User code (nullable)
     * @param PhoneNumber|null $phoneNumber Phone number (nullable)
     * @param FacultyId|null $facultyId Faculty ID (nullable)
     * @param DepartmentId|null $departmentId Department ID (nullable)
     * @return self
     */
    public static function create(
        UserId $id,
        UserName $userName,
        FullName $fullName,
        Email $email,
        ?UserCode $userCode = null,
        ?PhoneNumber $phoneNumber = null,
        ?FacultyId $facultyId = null,
        ?DepartmentId $departmentId = null,
    ): self {
        $user = new self(
            $id,
            $userName,
            $fullName,
            $email,
            $userCode,
            $phoneNumber,
            $facultyId,
            $departmentId,
        );

        $user->recordEvent(new UserWasCreated(
            userId: $id->toString(),
            email: (string) $email,
            fullName: $fullName->fullName(),
            userName: $userName->toString(),
            userCode: $userCode?->toString(),
            facultyId: $facultyId?->toString(),
            departmentId: $departmentId?->toString(),
        ));

        return $user;
    }

    /**
     * Update user profile.
     *
     * @param FullName $fullName Full name
     * @param Email $email Email address
     * @param PhoneNumber|null $phoneNumber Phone number (nullable)
     * @return void
     */
    public function updateProfile(
        FullName $fullName,
        Email $email,
        ?PhoneNumber $phoneNumber = null,
    ): void {
        $changes = [];

        if (!$this->fullName->equals($fullName)) {
            $changes['full_name'] = [
                'old' => $this->fullName->fullName(),
                'new' => $fullName->fullName(),
            ];
            $this->fullName = $fullName;
        }

        if (!$this->email->equals($email)) {
            $changes['email'] = [
                'old' => (string) $this->email,
                'new' => (string) $email,
            ];
            $this->email = $email;
        }

        if (null !== $phoneNumber && !$this->phoneNumber->equals($phoneNumber)) {
            $changes['phone_number'] = [
                'old' => $this->phoneNumber->toString(),
                'new' => $phoneNumber->toString(),
            ];
            $this->phoneNumber = $phoneNumber;
        }

        if (!empty($changes)) {
            $this->recordEvent(new UserWasUpdated(
                userId: $this->id->toString(),
                changes: $changes,
            ));
        }
    }

    /**
     * Assign user to faculty.
     *
     * @param FacultyId $facultyId Faculty ID
     * @return void
     */
    public function assignToFaculty(FacultyId $facultyId): void
    {
        if (null !== $this->facultyId && $this->facultyId->equals($facultyId)) {
            return; // Already assigned
        }

        $this->facultyId = $facultyId;

        $this->recordEvent(new UserWasAssignedToFaculty(
            userId: $this->id->toString(),
            facultyId: $facultyId->toString(),
        ));
    }

    /**
     * Assign user to department.
     *
     * @param DepartmentId $departmentId Department ID
     * @return void
     */
    public function assignToDepartment(DepartmentId $departmentId): void
    {
        if (null !== $this->departmentId && $this->departmentId->equals($departmentId)) {
            return; // Already assigned
        }

        $this->departmentId = $departmentId;

        $this->recordEvent(new UserWasAssignedToDepartment(
            userId: $this->id->toString(),
            departmentId: $departmentId->toString(),
        ));
    }

    /**
     * Get user ID.
     *
     * @return UserId
     */
    public function id(): UserId
    {
        return $this->id;
    }

    /**
     * Get username.
     *
     * @return UserName
     */
    public function userName(): UserName
    {
        return $this->userName;
    }

    /**
     * Get full name.
     *
     * @return FullName
     */
    public function fullName(): FullName
    {
        return $this->fullName;
    }

    /**
     * Get email.
     *
     * @return Email
     */
    public function email(): Email
    {
        return $this->email;
    }

    /**
     * Get user code.
     *
     * @return UserCode|null
     */
    public function userCode(): ?UserCode
    {
        return $this->userCode;
    }

    /**
     * Get phone number.
     *
     * @return PhoneNumber
     */
    public function phoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    /**
     * Get faculty ID.
     *
     * @return FacultyId|null
     */
    public function facultyId(): ?FacultyId
    {
        return $this->facultyId;
    }

    /**
     * Get department ID.
     *
     * @return DepartmentId|null
     */
    public function departmentId(): ?DepartmentId
    {
        return $this->departmentId;
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
