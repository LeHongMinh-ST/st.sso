<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\IdentityAccess\Application\DTOs\ChangePasswordDTO;
use App\IdentityAccess\Application\UseCases\ChangePasswordUseCase;
use App\IdentityAccess\Infrastructure\Services\UserIdentityBridgeService;
use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
use App\OrganizationalStructure\Application\UseCases\AssignUserToDepartmentUseCase;
use App\OrganizationalStructure\Application\UseCases\AssignUserToFacultyUseCase;
use App\OrganizationalStructure\Application\UseCases\CreateUserUseCase;
use App\OrganizationalStructure\Application\UseCases\FindUsersUseCase;
use App\OrganizationalStructure\Application\UseCases\FindUserUseCase;
use App\OrganizationalStructure\Application\UseCases\UpdateUserProfileUseCase;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use App\OrganizationalStructure\Infrastructure\Http\Requests\CreateUserRequest;
use App\OrganizationalStructure\Infrastructure\Http\Requests\UpdateUserRequest;
use App\OrganizationalStructure\Infrastructure\Http\Resources\User\UserResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * User API controller.
 * Handles HTTP requests for User aggregate.
 */
final class UserController
{
    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly FindUserUseCase $findUserUseCase,
        private readonly FindUsersUseCase $findUsersUseCase,
        private readonly UpdateUserProfileUseCase $updateUserProfileUseCase,
        private readonly AssignUserToFacultyUseCase $assignUserToFacultyUseCase,
        private readonly AssignUserToDepartmentUseCase $assignUserToDepartmentUseCase,
        private readonly ChangePasswordUseCase $changePasswordUseCase,
        private readonly UserIdentityBridgeService $bridgeService,
    ) {
    }

    /**
     * List users with pagination and filters.
     *
     * SECURITY:
     * - Permission check required
     * - Generic error messages
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Check permission
        $currentUser = Auth::guard('api')->user();
        if (null === $currentUser || !$currentUser->can('viewAny', EloquentUser::class)) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Insufficient permissions',
            ], 403);
        }

        try {
            // Get filters from request
            $filters = [
                'faculty_id' => $request->input('faculty_id'),
                'department_id' => $request->input('department_id'),
                'search' => $request->input('search'),
                'roles' => $request->input('roles') ? explode(',', $request->input('roles')) : null,
            ];

            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 15);

            // Use FindUsersUseCase
            $users = $this->findUsersUseCase->execute($filters, $page, $perPage);

            return UserResource::collection($users)->response();
        } catch (Exception $e) {
            Log::error('User listing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to retrieve users',
            ], 500);
        }
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
     * Supports both integer ID and UUID for backward compatibility.
     *
     * SECURITY:
     * - Permission check required
     * - Generic error messages
     *
     * @param string $identifier User ID (UUID) or integer ID
     * @return UserResource|JsonResponse
     */
    public function show(string $identifier): UserResource|JsonResponse
    {
        // Check permission
        $currentUser = Auth::guard('api')->user();
        if (null === $currentUser) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Authentication required',
            ], 401);
        }

        try {
            // Resolve identifier (supports both UUID and integer ID)
            $userId = $this->resolveUserId($identifier);

            if (null === $userId) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'User not found',
                ], 404);
            }

            // Find user
            $user = $this->findUserUseCase->execute($userId);

            // Check permission to view this specific user
            // Get Eloquent User for policy check
            $eloquentUser = $this->bridgeService->getEloquentUser($user);
            if (null === $eloquentUser || !$currentUser->can('view', $eloquentUser)) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Insufficient permissions',
                ], 403);
            }

            return new UserResource($user);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('User retrieval error', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to retrieve user',
            ], 500);
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

    /**
     * Reset user password to default 'password'.
     *
     * SECURITY:
     * - Permission check required (super admin or same faculty)
     * - Generic error messages
     *
     * @param string $identifier User ID (UUID) or integer ID
     * @return JsonResponse
     */
    public function resetPassword(string $identifier): JsonResponse
    {
        try {
            $currentUser = Auth::guard('api')->user();
            if (null === $currentUser) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required',
                ], 401);
            }

            // Resolve identifier
            $userId = $this->resolveUserId($identifier);
            if (null === $userId) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'User not found',
                ], 404);
            }

            // Find user
            $user = $this->findUserUseCase->execute($userId);
            $eloquentUser = $this->bridgeService->getEloquentUser($user);

            if (null === $eloquentUser) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'User not found',
                ], 404);
            }

            // Check permission: super admin or same faculty
            if (!$currentUser->isSuperAdmin() && $currentUser->faculty_id !== $eloquentUser->faculty_id) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Insufficient permissions',
                ], 403);
            }

            // Get UserIdentity
            $userIdentity = $this->bridgeService->getUserIdentity($eloquentUser);
            if (null === $userIdentity) {
                return response()->json([
                    'error' => 'Internal Server Error',
                    'message' => 'Failed to reset password',
                ], 500);
            }

            // Reset password using ChangePasswordUseCase
            // Note: ChangePasswordUseCase requires current password, but for reset we use a special flow
            // For now, we'll use a default password 'password'
            $dto = new ChangePasswordDTO(
                currentPassword: '', // Not required for admin reset
                newPassword: 'password',
            );

            // Use ChangePasswordUseCase (may need to create ResetPasswordUseCase later)
            $this->changePasswordUseCase->execute($userIdentity->id()->toString(), $dto);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('Password reset error', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to reset password',
            ], 500);
        }
    }

    /**
     * Resolve user ID from identifier (supports both UUID and integer ID).
     *
     * @param string $identifier UUID or integer ID
     * @return string|null UUID string or null if not found
     */
    private function resolveUserId(string $identifier): ?string
    {
        // Check if it's a UUID format
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $identifier)) {
            return $identifier;
        }

        // Check if it's an integer ID
        if (is_numeric($identifier)) {
            $hasUuidColumn = Schema::hasColumn('users', 'uuid');
            if ($hasUuidColumn) {
                $user = EloquentUser::find((int) $identifier);
                if (null !== $user && null !== $user->uuid) {
                    return $user->uuid;
                }
            }

            // Fallback: generate deterministic UUID
            return $this->generateDeterministicUuid('users', (int) $identifier);
        }

        return null;
    }

    /**
     * Generate deterministic UUID from integer ID.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function generateDeterministicUuid(string $table, int $integerId): string
    {
        $namespace = \Ramsey\Uuid\Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $name = "{$table}:{$integerId}";

        return \Ramsey\Uuid\Uuid::uuid5($namespace, $name)->toString();
    }
}
