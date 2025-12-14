<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\Enums\Status;
use App\OrganizationalStructure\Application\DTOs\CreateDepartmentDTO;
use App\OrganizationalStructure\Domain\Entities\Department;
use App\OrganizationalStructure\Domain\Exceptions\FacultyNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\DepartmentRepositoryInterface;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for creating a new department.
 */
final class CreateDepartmentUseCase
{
    public function __construct(
        private readonly DepartmentRepositoryInterface $departmentRepository,
        private readonly FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    /**
     * Execute create department use case.
     *
     * @param CreateDepartmentDTO $dto Create department DTO
     * @return Department Created department entity
     * @throws FacultyNotFoundException
     */
    public function execute(CreateDepartmentDTO $dto): Department
    {
        return DB::transaction(function () use ($dto) {
            // Verify faculty exists
            $facultyId = FacultyId::fromString($dto->facultyId);
            $faculty = $this->facultyRepository->findById($facultyId);

            if (null === $faculty) {
                throw FacultyNotFoundException::withId($dto->facultyId);
            }

            // Create department entity
            $departmentId = DepartmentId::generate();
            $department = Department::create(
                $departmentId,
                $dto->name,
                $facultyId,
                Status::Active,
            );

            // Save department entity
            $this->departmentRepository->save($department);

            // Save domain events to outbox
            $events = $department->pullDomainEvents();
            foreach ($events as $event) {
                OutboxEvent::create([
                    'id' => Str::uuid()->toString(),
                    'aggregate_type' => 'department',
                    'aggregate_id' => $departmentId->toString(),
                    'event_type' => $event::class,
                    'payload' => $event->toPayload(),
                ]);
            }

            return $department;
        });
    }
}
