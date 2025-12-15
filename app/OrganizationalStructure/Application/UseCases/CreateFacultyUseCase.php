<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\SharedKernel\Domain\Enums\Status;
use App\OrganizationalStructure\Application\DTOs\CreateFacultyDTO;
use App\OrganizationalStructure\Domain\Entities\Faculty;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for creating a new faculty.
 */
final class CreateFacultyUseCase
{
    public function __construct(
        private readonly FacultyRepositoryInterface $facultyRepository,
        private readonly OutboxEventRepositoryInterface $outboxEventRepository,
    ) {
    }

    /**
     * Execute create faculty use case.
     *
     * @param CreateFacultyDTO $dto Create faculty DTO
     * @return Faculty Created faculty entity
     */
    public function execute(CreateFacultyDTO $dto): Faculty
    {
        return DB::transaction(function () use ($dto) {
            // Create faculty entity
            $facultyId = FacultyId::generate();
            $faculty = Faculty::create(
                $facultyId,
                $dto->name,
                Status::Active,
                $dto->description,
            );

            // Save faculty entity
            $this->facultyRepository->save($faculty);

            // Save domain events to outbox
            $events = $faculty->pullDomainEvents();
            foreach ($events as $event) {
                $this->outboxEventRepository->store(
                    Str::uuid()->toString(),
                    'faculty',
                    $facultyId->toString(),
                    $event::class,
                    $event->toPayload()
                );
            }

            return $faculty;
        });
    }
}
