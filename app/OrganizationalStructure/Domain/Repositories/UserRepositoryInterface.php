<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Repositories;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Domain\ValueObjects\UserCode;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use App\OrganizationalStructure\Domain\ValueObjects\UserName;
use App\SharedKernel\Domain\ValueObjects\Email;

/**
 * User repository interface.
 * Defines methods for persisting and retrieving User aggregates.
 */
interface UserRepositoryInterface
{
    /**
     * Save a user aggregate.
     *
     * @param User $user User aggregate
     * @return void
     */
    public function save(User $user): void;

    /**
     * Find user by ID.
     *
     * @param UserId $id User ID
     * @return User|null
     */
    public function findById(UserId $id): ?User;

    /**
     * Find user by email.
     *
     * @param Email $email Email address
     * @return User|null
     */
    public function findByEmail(Email $email): ?User;

    /**
     * Find user by username.
     *
     * @param UserName $userName Username
     * @return User|null
     */
    public function findByUserName(UserName $userName): ?User;

    /**
     * Find user by user code.
     *
     * @param UserCode $userCode User code
     * @return User|null
     */
    public function findByUserCode(UserCode $userCode): ?User;

    /**
     * Find users by faculty ID.
     *
     * @param FacultyId $facultyId Faculty ID
     * @return array<User>
     */
    public function findByFacultyId(FacultyId $facultyId): array;

    /**
     * Find users by department ID.
     *
     * @param DepartmentId $departmentId Department ID
     * @return array<User>
     */
    public function findByDepartmentId(DepartmentId $departmentId): array;

    /**
     * Delete user by ID.
     *
     * @param UserId $id User ID
     * @return void
     */
    public function delete(UserId $id): void;

    /**
     * Check if user exists by email.
     *
     * @param Email $email Email address
     * @return bool
     */
    public function existsByEmail(Email $email): bool;

    /**
     * Check if user exists by username.
     *
     * @param UserName $userName Username
     * @return bool
     */
    public function existsByUserName(UserName $userName): bool;

    /**
     * Check if user exists by user code.
     *
     * @param UserCode $userCode User code
     * @return bool
     */
    public function existsByUserCode(UserCode $userCode): bool;
}
