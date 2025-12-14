<?php

declare(strict_types=1);

namespace Tests\Unit\OrganizationalStructure\Domain\Aggregates;

use App\OrganizationalStructure\Domain\Aggregates\User;
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
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    /**
     * Test that create method creates user and records event.
     */
    public function test_create_user_records_event(): void
    {
        $userId = UserId::generate();
        $userName = UserName::fromString('john.doe');
        $fullName = FullName::fromParts('John', 'Doe');
        $email = Email::fromString('john@example.com');

        $user = User::create($userId, $userName, $fullName, $email);

        $events = $user->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserWasCreated::class, $events[0]);
        $this->assertEquals($userId->toString(), $events[0]->userId);
        $this->assertEquals((string) $email, $events[0]->email);
    }

    /**
     * Test that updateProfile updates and records event.
     */
    public function test_update_profile_updates_and_records_event(): void
    {
        $user = $this->createUser();
        $user->pullDomainEvents(); // Clear initial event

        $newFullName = FullName::fromParts('Jane', 'Smith');
        $newEmail = Email::fromString('jane@example.com');
        $newPhone = PhoneNumber::fromString('+84123456789');

        $user->updateProfile($newFullName, $newEmail, $newPhone);

        $events = $user->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserWasUpdated::class, $events[0]);
        $this->assertArrayHasKey('full_name', $events[0]->changes);
        $this->assertArrayHasKey('email', $events[0]->changes);
        $this->assertArrayHasKey('phone_number', $events[0]->changes);
    }

    /**
     * Test that updateProfile does not record event if nothing changed.
     */
    public function test_update_profile_no_event_if_no_changes(): void
    {
        $user = $this->createUser();
        $user->pullDomainEvents(); // Clear initial event

        $user->updateProfile(
            $user->fullName(),
            $user->email(),
            $user->phoneNumber(),
        );

        $events = $user->pullDomainEvents();

        $this->assertCount(0, $events);
    }

    /**
     * Test that assignToFaculty assigns and records event.
     */
    public function test_assign_to_faculty_assigns_and_records_event(): void
    {
        $user = $this->createUser();
        $user->pullDomainEvents(); // Clear initial event

        $facultyId = FacultyId::generate();

        $user->assignToFaculty($facultyId);

        $events = $user->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserWasAssignedToFaculty::class, $events[0]);
        $this->assertEquals($facultyId->toString(), $events[0]->facultyId);
        $this->assertEquals($facultyId->toString(), $user->facultyId()?->toString());
    }

    /**
     * Test that assignToFaculty does not duplicate event if already assigned.
     */
    public function test_assign_to_faculty_no_duplicate_event_if_already_assigned(): void
    {
        $user = $this->createUser();
        $facultyId = FacultyId::generate();

        $user->assignToFaculty($facultyId);
        $user->pullDomainEvents(); // Clear events

        $user->assignToFaculty($facultyId);

        $events = $user->pullDomainEvents();

        $this->assertCount(0, $events);
    }

    /**
     * Test that assignToDepartment assigns and records event.
     */
    public function test_assign_to_department_assigns_and_records_event(): void
    {
        $user = $this->createUser();
        $user->pullDomainEvents(); // Clear initial event

        $departmentId = DepartmentId::generate();

        $user->assignToDepartment($departmentId);

        $events = $user->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserWasAssignedToDepartment::class, $events[0]);
        $this->assertEquals($departmentId->toString(), $events[0]->departmentId);
        $this->assertEquals($departmentId->toString(), $user->departmentId()?->toString());
    }

    /**
     * Test that assignToDepartment does not duplicate event if already assigned.
     */
    public function test_assign_to_department_no_duplicate_event_if_already_assigned(): void
    {
        $user = $this->createUser();
        $departmentId = DepartmentId::generate();

        $user->assignToDepartment($departmentId);
        $user->pullDomainEvents(); // Clear events

        $user->assignToDepartment($departmentId);

        $events = $user->pullDomainEvents();

        $this->assertCount(0, $events);
    }

    /**
     * Test that pullDomainEvents returns and clears events.
     */
    public function test_pull_domain_events_returns_and_clears_events(): void
    {
        $user = $this->createUser();

        $events1 = $user->pullDomainEvents();
        $events2 = $user->pullDomainEvents();

        $this->assertCount(1, $events1);
        $this->assertCount(0, $events2);
    }

    /**
     * Test that getters return correct values.
     */
    public function test_getters_return_correct_values(): void
    {
        $userId = UserId::generate();
        $userName = UserName::fromString('john.doe');
        $fullName = FullName::fromParts('John', 'Doe');
        $email = Email::fromString('john@example.com');
        $userCode = UserCode::fromString('ST001');
        $phoneNumber = PhoneNumber::fromString('+84123456789');
        $facultyId = FacultyId::generate();
        $departmentId = DepartmentId::generate();

        $user = User::create(
            $userId,
            $userName,
            $fullName,
            $email,
            $userCode,
            $phoneNumber,
            $facultyId,
            $departmentId,
        );

        $this->assertTrue($userId->equals($user->id()));
        $this->assertTrue($userName->equals($user->userName()));
        $this->assertTrue($fullName->equals($user->fullName()));
        $this->assertTrue($email->equals($user->email()));
        $this->assertTrue($userCode->equals($user->userCode()));
        $this->assertTrue($phoneNumber->equals($user->phoneNumber()));
        $this->assertTrue($facultyId->equals($user->facultyId()));
        $this->assertTrue($departmentId->equals($user->departmentId()));
    }

    /**
     * Test that multiple events are recorded in correct order.
     */
    public function test_multiple_events_recorded_in_correct_order(): void
    {
        $user = $this->createUser();
        $user->pullDomainEvents(); // Clear initial event

        $facultyId = FacultyId::generate();
        $departmentId = DepartmentId::generate();

        $user->assignToFaculty($facultyId);
        $user->assignToDepartment($departmentId);

        $events = $user->pullDomainEvents();

        $this->assertCount(2, $events);
        $this->assertInstanceOf(UserWasAssignedToFaculty::class, $events[0]);
        $this->assertInstanceOf(UserWasAssignedToDepartment::class, $events[1]);
    }

    /**
     * Create a test user.
     *
     * @return User
     */
    private function createUser(): User
    {
        return User::create(
            UserId::generate(),
            UserName::fromString('test.user'),
            FullName::fromParts('Test', 'User'),
            Email::fromString('test@example.com'),
        );
    }
}
