<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Http\Controllers;

use App\OrganizationalStructure\Application\DTOs\CreateFacultyDTO;
use App\OrganizationalStructure\Application\UseCases\CreateFacultyUseCase;
use App\OrganizationalStructure\Application\UseCases\FindUsersUseCase;
use App\OrganizationalStructure\Domain\Exceptions\FacultyNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty as EloquentFaculty;
use App\OrganizationalStructure\Infrastructure\Http\Requests\CreateFacultyRequest;
use App\OrganizationalStructure\Infrastructure\Http\Resources\Department\DepartmentResource;
use App\OrganizationalStructure\Infrastructure\Http\Resources\Faculty\FacultyCollection;
use App\OrganizationalStructure\Infrastructure\Http\Resources\Faculty\FacultyResource;
use App\OrganizationalStructure\Infrastructure\Http\Resources\User\UserResource;
use App\SharedKernel\Helpers\Constants;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Faculty API controller.
 * Handles HTTP requests for Faculty aggregate.
 */
final class FacultyController
{
    public function __construct(
        private readonly CreateFacultyUseCase $createFacultyUseCase,
        private readonly FacultyRepositoryInterface $facultyRepository,
        private readonly FindUsersUseCase $findUsersUseCase,
    ) {
    }

    /**
     * Display a listing of faculties with pagination.
     *
     * SECURITY:
     * - Permission check required
     * - Generic error messages
     *
     * @param Request $request
     * @return FacultyCollection|JsonResponse
     */
    public function index(Request $request): FacultyCollection|JsonResponse
    {
        try {
            // Check permission
            $currentUser = Auth::guard('api')->user();
            if (null === $currentUser || !$currentUser->can('viewAny', EloquentFaculty::class)) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Insufficient permissions',
                ], 403);
            }

            // Get search filter
            $search = $request->input('search');

            // Get all faculties
            $faculties = $this->facultyRepository->findAll();

            // Apply search filter if provided
            if (null !== $search && '' !== $search) {
                $faculties = array_filter($faculties, fn ($faculty): bool => false !== mb_stripos($faculty->name(), $search));
            }

            // Paginate manually (since repository returns array)
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', Constants::PER_PAGE);
            $total = count($faculties);
            $offset = ($page - 1) * $perPage;
            $paginatedFaculties = array_slice($faculties, $offset, $perPage);

            return new FacultyCollection($paginatedFaculties);
        } catch (Exception $e) {
            Log::error('Faculty listing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to retrieve faculties',
            ], 500);
        }
    }

    /**
     * Get all faculties (no pagination).
     *
     * SECURITY:
     * - Permission check required
     * - Generic error messages
     *
     * @return FacultyCollection|JsonResponse
     */
    public function all(): FacultyCollection|JsonResponse
    {
        try {
            // Check permission
            $currentUser = Auth::guard('api')->user();
            if (null === $currentUser) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required',
                ], 401);
            }

            $faculties = $this->facultyRepository->findAll();

            return new FacultyCollection($faculties);
        } catch (Exception $e) {
            Log::error('Faculty listing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to retrieve faculties',
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

    /**
     * Get users by faculty.
     *
     * SECURITY:
     * - Permission check required (super admin or same faculty)
     * - Generic error messages
     *
     * @param string $identifier Faculty ID (UUID) or integer ID
     * @param Request $request
     * @return JsonResponse
     */
    public function users(string $identifier, Request $request): JsonResponse
    {
        try {
            $currentUser = Auth::guard('api')->user();
            if (null === $currentUser) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required',
                ], 401);
            }

            // Resolve faculty ID
            $facultyId = $this->resolveFacultyId($identifier);
            if (null === $facultyId) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            // Find faculty
            $faculty = $this->facultyRepository->findById(FacultyId::fromString($facultyId));
            if (null === $faculty) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            // Check permission: super admin or same faculty
            $eloquentFaculty = EloquentFaculty::where('uuid', $facultyId)->first()
                ?? EloquentFaculty::find((int) $identifier);
            if (null === $eloquentFaculty) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            if (!$currentUser->isSuperAdmin() && $currentUser->faculty_id !== $eloquentFaculty->id) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Insufficient permissions',
                ], 403);
            }

            // Get filters
            $search = $request->input('q', $request->input('search'));

            // Get users directly from Eloquent model
            // (FindUsersUseCase doesn't support exclude roles yet)
            $query = EloquentUser::where('faculty_id', $eloquentFaculty->id)
                ->whereNotIn('role', [Role::SuperAdmin->value, Role::Student->value]);

            if (null !== $search && '' !== $search) {
                $query->where(function ($q) use ($search): void {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('user_name', 'like', "%{$search}%");
                });
            }

            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', Constants::PER_PAGE);

            $users = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Return Eloquent users wrapped in UserResource
            // Note: In full DDD migration, we'd convert to Domain aggregates
            return UserResource::collection($users)->response();
        } catch (FacultyNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Faculty not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('Faculty users listing error', [
                'faculty_id' => $identifier,
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
     * Get teachers by faculty.
     *
     * SECURITY:
     * - Permission check required (super admin or same faculty)
     * - Generic error messages
     *
     * @param string $identifier Faculty ID (UUID) or integer ID
     * @param Request $request
     * @return JsonResponse
     */
    public function teachers(string $identifier, Request $request): JsonResponse
    {
        try {
            $currentUser = Auth::guard('api')->user();
            if (null === $currentUser) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required',
                ], 401);
            }

            // Resolve faculty ID
            $facultyId = $this->resolveFacultyId($identifier);
            if (null === $facultyId) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            // Find faculty
            $faculty = $this->facultyRepository->findById(FacultyId::fromString($facultyId));
            if (null === $faculty) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            // Check permission: super admin or same faculty
            $eloquentFaculty = EloquentFaculty::where('uuid', $facultyId)->first()
                ?? EloquentFaculty::find((int) $identifier);
            if (null === $eloquentFaculty) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            if (!$currentUser->isSuperAdmin() && $currentUser->faculty_id !== $eloquentFaculty->id) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Insufficient permissions',
                ], 403);
            }

            // Get filters
            $search = $request->input('q', $request->input('search'));

            // Get teachers directly from Eloquent model
            $query = $eloquentFaculty->teachers();

            if (null !== $search && '' !== $search) {
                $query->where(function ($q) use ($search): void {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('user_name', 'like', "%{$search}%");
                });
            }

            $users = $query->orderBy('users.created_at', 'desc')
                ->paginate(Constants::PER_PAGE);

            return UserResource::collection($users)->response();
        } catch (FacultyNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Faculty not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('Faculty teachers listing error', [
                'faculty_id' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to retrieve teachers',
            ], 500);
        }
    }

    /**
     * Get departments by faculty.
     *
     * SECURITY:
     * - Generic error messages
     *
     * @param string $identifier Faculty ID (UUID) or integer ID
     * @param Request $request
     * @return JsonResponse
     */
    public function departments(string $identifier, Request $request): JsonResponse
    {
        try {
            // Resolve faculty ID
            $facultyId = $this->resolveFacultyId($identifier);
            if (null === $facultyId) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            // Find faculty
            $faculty = $this->facultyRepository->findById(FacultyId::fromString($facultyId));
            if (null === $faculty) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            // Get departments from Eloquent model
            $eloquentFaculty = EloquentFaculty::where('uuid', $facultyId)->first()
                ?? EloquentFaculty::find((int) $identifier);
            if (null === $eloquentFaculty) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Faculty not found',
                ], 404);
            }

            // Get search filter
            $search = $request->input('q', $request->input('search'));

            // Query departments
            $query = $eloquentFaculty->departments()->orderBy('created_at', 'desc');

            if (null !== $search && '' !== $search) {
                $query->where('name', 'like', "%{$search}%");
            }

            $departments = $query->paginate(Constants::PER_PAGE);

            return DepartmentResource::collection($departments)->response();
        } catch (FacultyNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Faculty not found',
            ], 404);
        } catch (Exception $e) {
            Log::error('Faculty departments listing error', [
                'faculty_id' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Failed to retrieve departments',
            ], 500);
        }
    }

    /**
     * Resolve faculty ID from identifier (supports both UUID and integer ID).
     *
     * @param string $identifier UUID or integer ID
     * @return string|null UUID string or null if not found
     */
    private function resolveFacultyId(string $identifier): ?string
    {
        // Check if it's a UUID format
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $identifier)) {
            return $identifier;
        }

        // Check if it's an integer ID
        if (is_numeric($identifier)) {
            $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn('faculties', 'uuid');
            if ($hasUuidColumn) {
                $faculty = EloquentFaculty::find((int) $identifier);
                if (null !== $faculty && null !== $faculty->uuid) {
                    return $faculty->uuid;
                }
            }

            // Fallback: generate deterministic UUID
            return $this->generateDeterministicUuid('faculties', (int) $identifier);
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
