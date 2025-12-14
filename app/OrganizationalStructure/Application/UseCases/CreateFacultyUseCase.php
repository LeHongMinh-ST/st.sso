<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\Enums\Status;
use App\OrganizationalStructure\Application\DTOs\CreateFacultyDTO;
use App\OrganizationalStructure\Domain\Entities\Faculty;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\SharedKernel\Infrastructure\Outbox\OutboxEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Use case for creating a new faculty.
 */
final class CreateFacultyUseCase
{
    public function __construct(
        private readonly FacultyRepositoryInterface $facultyRepository,
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
                OutboxEvent::create([
                    'id' => Str::uuid()->toString(),
                    'aggregate_type' => 'faculty',
                    'aggregate_id' => $facultyId->toString(),
                    'event_type' => $event::class,
                    'payload' => $event->toPayload(),
                ]);
            }

            return $faculty;
        });
    }
}
