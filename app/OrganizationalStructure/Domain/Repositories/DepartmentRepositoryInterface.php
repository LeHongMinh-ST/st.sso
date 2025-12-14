<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Repositories;

use App\OrganizationalStructure\Domain\Entities\Department;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;

/**
 * Department repository interface.
 * Defines methods for persisting and retrieving Department entities.
 */
interface DepartmentRepositoryInterface
{
    /**
     * Save a department entity.
     *
     * @param Department $department Department entity
     * @return void
     */
    public function save(Department $department): void;

    /**
     * Find department by ID.
     *
     * @param DepartmentId $id Department ID
     * @return Department|null
     */
    public function findById(DepartmentId $id): ?Department;

    /**
     * Find department by name.
     *
     * @param string $name Department name
     * @return Department|null
     */
    public function findByName(string $name): ?Department;

    /**
     * Find departments by faculty ID.
     *
     * @param FacultyId $facultyId Faculty ID
     * @return array<Department>
     */
    public function findByFacultyId(FacultyId $facultyId): array;

    /**
     * Find all departments.
     *
     * @return array<Department>
     */
    public function findAll(): array;

    /**
     * Delete department by ID.
     *
     * @param DepartmentId $id Department ID
     * @return void
     */
    public function delete(DepartmentId $id): void;

    /**
     * Check if department exists by name.
     *
     * @param string $name Department name
     * @return bool
     */
    public function existsByName(string $name): bool;
}
