<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\DTOs\CreateDepartmentDTO;
use App\OrganizationalStructure\Application\UseCases\CreateDepartmentUseCase;
use App\OrganizationalStructure\Domain\Exceptions\DepartmentNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\DepartmentRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\DepartmentId;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Department controller for OrganizationalStructure context.
 * Handles HTTP requests and delegates to Use Cases.
 */
final class DepartmentController
{
    public function __construct(
        private readonly CreateDepartmentUseCase $createDepartmentUseCase,
        private readonly DepartmentRepositoryInterface $departmentRepository,
    ) {
    }

    /**
     * Create a new department.
     *
     * @param Request $request HTTP request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $dto = CreateDepartmentDTO::fromArray($request->all());

        try {
            $department = $this->createDepartmentUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $department->id()->toString(),
                    'name' => $department->name(),
                    'status' => $department->status()->value,
                    'faculty_id' => $department->facultyId()->toString(),
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
     * Get department by ID.
     *
     * @param string $id Department ID (UUID)
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $departmentId = DepartmentId::fromString($id);
            $department = $this->departmentRepository->findById($departmentId);

            if (null === $department) {
                throw DepartmentNotFoundException::withId($id);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $department->id()->toString(),
                    'name' => $department->name(),
                    'status' => $department->status()->value,
                    'faculty_id' => $department->facultyId()->toString(),
                ],
            ]);
        } catch (DepartmentNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get all departments.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $departments = $this->departmentRepository->findAll();

            return response()->json([
                'success' => true,
                'data' => array_map(fn ($department) => [
                    'id' => $department->id()->toString(),
                    'name' => $department->name(),
                    'status' => $department->status()->value,
                    'faculty_id' => $department->facultyId()->toString(),
                ], $departments),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
