<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Domain\Repositories;

use App\OrganizationalStructure\Domain\Entities\Faculty;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;

/**
 * Faculty repository interface.
 * Defines methods for persisting and retrieving Faculty entities.
 */
interface FacultyRepositoryInterface
{
    /**
     * Save a faculty entity.
     *
     * @param Faculty $faculty Faculty entity
     * @return void
     */
    public function save(Faculty $faculty): void;

    /**
     * Find faculty by ID.
     *
     * @param FacultyId $id Faculty ID
     * @return Faculty|null
     */
    public function findById(FacultyId $id): ?Faculty;

    /**
     * Find faculty by name.
     *
     * @param string $name Faculty name
     * @return Faculty|null
     */
    public function findByName(string $name): ?Faculty;

    /**
     * Find all faculties.
     *
     * @return array<Faculty>
     */
    public function findAll(): array;

    /**
     * Delete faculty by ID.
     *
     * @param FacultyId $id Faculty ID
     * @return void
     */
    public function delete(FacultyId $id): void;

    /**
     * Check if faculty exists by name.
     *
     * @param string $name Faculty name
     * @return bool
     */
    public function existsByName(string $name): bool;
}
