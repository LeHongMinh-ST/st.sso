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
use App\OrganizationalStructure\Infrastructure\Http\Requests\CreateUserRequest;
use App\OrganizationalStructure\Infrastructure\Http\Requests\UpdateUserRequest;
use App\OrganizationalStructure\Infrastructure\Http\Resources\User\UserResource;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * User API controller.
 * Handles HTTP requests for User aggregate.
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
     * Store a newly created user.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $dto = CreateUserDTO::fromArray($request->validated());
            $user = $this->createUserUseCase->execute($dto);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified user.
     *
     * @param string $id User ID (UUID)
     * @return UserResource|JsonResponse
     */
    public function show(string $id): UserResource|JsonResponse
    {
        try {
            $user = $this->findUserUseCase->execute($id);

            return new UserResource($user);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified user.
     * Handles both profile updates and assignment to faculty/department.
     *
     * @param UpdateUserRequest $request
     * @param string $id User ID (UUID)
     * @return UserResource|JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): UserResource|JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = null;

            // Handle profile update if provided
            if (isset($validated['first_name']) || isset($validated['last_name']) || isset($validated['email']) || isset($validated['phone'])) {
                $dto = UpdateUserProfileDTO::fromArray([
                    'first_name' => $validated['first_name'] ?? '',
                    'last_name' => $validated['last_name'] ?? '',
                    'email' => $validated['email'] ?? '',
                    'phone' => $validated['phone'] ?? null,
                ]);
                $user = $this->updateUserProfileUseCase->execute($id, $dto);
            }

            // Handle faculty assignment if provided
            if (isset($validated['faculty_id'])) {
                $user = $this->assignUserToFacultyUseCase->execute($id, $validated['faculty_id']);
            }

            // Handle department assignment if provided
            if (isset($validated['department_id'])) {
                $user = $this->assignUserToDepartmentUseCase->execute($id, $validated['department_id']);
            }

            // If no updates were made, fetch the user
            if (null === $user) {
                $user = $this->findUserUseCase->execute($id);
            }

            return new UserResource($user);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
