<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\DTOs\CreateFacultyDTO;
use App\OrganizationalStructure\Application\UseCases\CreateFacultyUseCase;
use App\OrganizationalStructure\Domain\Exceptions\FacultyNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Faculty controller for OrganizationalStructure context.
 * Handles HTTP requests and delegates to Use Cases.
 */
final class FacultyController
{
    public function __construct(
        private readonly CreateFacultyUseCase $createFacultyUseCase,
        private readonly FacultyRepositoryInterface $facultyRepository,
    ) {
    }

    /**
     * Create a new faculty.
     *
     * @param Request $request HTTP request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $dto = CreateFacultyDTO::fromArray($request->all());

        try {
            $faculty = $this->createFacultyUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $faculty->id()->toString(),
                    'name' => $faculty->name(),
                    'status' => $faculty->status()->value,
                    'description' => $faculty->description(),
                ],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get faculty by ID.
     *
     * @param string $id Faculty ID (UUID)
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $facultyId = FacultyId::fromString($id);
            $faculty = $this->facultyRepository->findById($facultyId);

            if (null === $faculty) {
                throw FacultyNotFoundException::withId($id);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $faculty->id()->toString(),
                    'name' => $faculty->name(),
                    'status' => $faculty->status()->value,
                    'description' => $faculty->description(),
                ],
            ]);
        } catch (FacultyNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Faculty not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get all faculties.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $faculties = $this->facultyRepository->findAll();

            return response()->json([
                'success' => true,
                'data' => array_map(fn ($faculty) => [
                    'id' => $faculty->id()->toString(),
                    'name' => $faculty->name(),
                    'status' => $faculty->status()->value,
                    'description' => $faculty->description(),
                ], $faculties),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
