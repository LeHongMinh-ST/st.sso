<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
use App\OrganizationalStructure\Application\UseCases\AssignUserToDepartmentUseCase;
use App\OrganizationalStructure\Application\UseCases\AssignUserToFacultyUseCase;
use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use App\OrganizationalStructure\Application\UseCases\FindUserUseCase;
use App\OrganizationalStructure\Application\UseCases\UpdateUserProfileUseCase;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * User controller for OrganizationalStructure context.
 * Handles HTTP requests and delegates to Use Cases.
 */
final class UserController
{
    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly FindUserUseCase $findUserUseCase,
        private readonly UpdateUserProfileUseCase $updateUserProfileUseCase,
        private readonly AssignUserToFacultyUseCase $assignUserToFacultyUseCase,
        private readonly AssignUserToDepartmentUseCase $assignUserToDepartmentUseCase,
    ) {
    }

    /**
     * Create a new user.
     *
     * @param Request $request HTTP request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $dto = CreateUserDTO::fromArray($request->all());

        try {
            $user = $this->createUserUseCase->execute($dto);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id()->toString(),
                    'user_name' => $user->userName()->toString(),
                    'email' => (string) $user->email(),
                    'full_name' => $user->fullName()->fullName(),
                    'user_code' => $user->userCode()?->toString(),
                    'phone' => $user->phoneNumber()->isNull() ? null : $user->phoneNumber()->toString(),
                    'faculty_id' => $user->facultyId()?->toString(),
                    'department_id' => $user->departmentId()?->toString(),
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
     * Get user by ID.
     *
     * @param string $id User ID (UUID)
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            /* $userId = UserId::fromString($id); */
            $user = $this->findUserUseCase->execute($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id()->toString(),
                    'user_name' => $user->userName()->toString(),
                    'email' => (string) $user->email(),
                    'full_name' => $user->fullName()->fullName(),
                    'user_code' => $user->userCode()?->toString(),
                    'phone' => $user->phoneNumber()->isNull() ? null : $user->phoneNumber()->toString(),
                    'faculty_id' => $user->facultyId()?->toString(),
                    'department_id' => $user->departmentId()?->toString(),
                ],
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update user profile.
     *
     * @param string $id User ID (UUID)
     * @param Request $request HTTP request
     * @return JsonResponse
     */
    public function update(string $id, Request $request): JsonResponse
    {
        try {
            $dto = UpdateUserProfileDTO::fromArray([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
            ]);

            $user = $this->updateUserProfileUseCase->execute($id, $dto);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id()->toString(),
                    'user_name' => $user->userName()->toString(),
                    'email' => (string) $user->email(),
                    'full_name' => $user->fullName()->fullName(),
                    'user_code' => $user->userCode()?->toString(),
                    'phone' => $user->phoneNumber()->isNull() ? null : $user->phoneNumber()->toString(),
                    'faculty_id' => $user->facultyId()?->toString(),
                    'department_id' => $user->departmentId()?->toString(),
                ],
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Assign user to faculty.
     *
     * @param string $id User ID (UUID)
     * @param Request $request HTTP request
     * @return JsonResponse
     */
    public function assignToFaculty(string $id, Request $request): JsonResponse
    {
        try {
            $facultyId = $request->input('faculty_id');

            $user = $this->assignUserToFacultyUseCase->execute($id, $facultyId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id()->toString(),
                    'faculty_id' => $user->facultyId()?->toString(),
                ],
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Assign user to department.
     *
     * @param string $id User ID (UUID)
     * @param Request $request HTTP request
     * @return JsonResponse
     */
    public function assignToDepartment(string $id, Request $request): JsonResponse
    {
        try {
            $departmentId = $request->input('department_id');

            $user = $this->assignUserToDepartmentUseCase->execute($id, $departmentId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id()->toString(),
                    'department_id' => $user->departmentId()?->toString(),
                ],
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
