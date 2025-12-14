<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\DTOs\CreateFacultyDTO;
use App\OrganizationalStructure\Application\UseCases\CreateFacultyUseCase;
use App\OrganizationalStructure\Domain\Exceptions\FacultyNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Infrastructure\Http\Requests\CreateFacultyRequest;
use App\OrganizationalStructure\Infrastructure\Http\Resources\Faculty\FacultyCollection;
use App\OrganizationalStructure\Infrastructure\Http\Resources\Faculty\FacultyResource;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Faculty API controller.
 * Handles HTTP requests for Faculty aggregate.
 */
final class FacultyController
{
    public function __construct(
        private readonly CreateFacultyUseCase $createFacultyUseCase,
        private readonly FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    /**
     * Display a listing of faculties.
     *
     * @return FacultyCollection|JsonResponse
     */
    public function index(): FacultyCollection|JsonResponse
    {
        try {
            $faculties = $this->facultyRepository->findAll();

            return new FacultyCollection($faculties);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created faculty.
     *
     * @param CreateFacultyRequest $request
     * @return JsonResponse
     */
    public function store(CreateFacultyRequest $request): JsonResponse
    {
        try {
            $dto = CreateFacultyDTO::fromArray($request->validated());
            $faculty = $this->createFacultyUseCase->execute($dto);

            return (new FacultyResource($faculty))
                ->response()
                ->setStatusCode(201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified faculty.
     *
     * @param string $id Faculty ID (UUID)
     * @return FacultyResource|JsonResponse
     */
    public function show(string $id): FacultyResource|JsonResponse
    {
        try {
            $facultyId = FacultyId::fromString($id);
            $faculty = $this->facultyRepository->findById($facultyId);

            if (null === $faculty) {
                throw FacultyNotFoundException::withId($id);
            }

            return new FacultyResource($faculty);
        } catch (FacultyNotFoundException $e) {
            return response()->json([
                'message' => 'Faculty not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
