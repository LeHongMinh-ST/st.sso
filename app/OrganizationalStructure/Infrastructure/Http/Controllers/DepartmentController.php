<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\DTOs\CreateDepartmentDTO;
use App\OrganizationalStructure\Application\UseCases\CreateDepartmentUseCase;
use App\OrganizationalStructure\Domain\Exceptions\DepartmentNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\DepartmentRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use App\OrganizationalStructure\Infrastructure\Http\Requests\CreateDepartmentRequest;
use App\OrganizationalStructure\Infrastructure\Http\Resources\Department\DepartmentCollection;
use App\OrganizationalStructure\Infrastructure\Http\Resources\Department\DepartmentResource;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Department API controller.
 * Handles HTTP requests for Department aggregate.
 */
final class DepartmentController
{
    public function __construct(
        private readonly CreateDepartmentUseCase $createDepartmentUseCase,
        private readonly DepartmentRepositoryInterface $departmentRepository,
    ) {
    }

    /**
     * Display a listing of departments.
     *
     * @return DepartmentCollection|JsonResponse
     */
    public function index(): DepartmentCollection|JsonResponse
    {
        try {
            $departments = $this->departmentRepository->findAll();

            return new DepartmentCollection($departments);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created department.
     *
     * @param CreateDepartmentRequest $request
     * @return JsonResponse
     */
    public function store(CreateDepartmentRequest $request): JsonResponse
    {
        try {
            $dto = CreateDepartmentDTO::fromArray($request->validated());
            $department = $this->createDepartmentUseCase->execute($dto);

            return (new DepartmentResource($department))
                ->response()
                ->setStatusCode(201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified department.
     *
     * @param string $id Department ID (UUID)
     * @return DepartmentResource|JsonResponse
     */
    public function show(string $id): DepartmentResource|JsonResponse
    {
        try {
            $departmentId = DepartmentId::fromString($id);
            $department = $this->departmentRepository->findById($departmentId);

            if (null === $department) {
                throw DepartmentNotFoundException::withId($id);
            }

            return new DepartmentResource($department);
        } catch (DepartmentNotFoundException $e) {
            return response()->json([
                'message' => 'Department not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
