<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Constants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::guard('api')->user()->can('viewAny', User::class)) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $users = User::query()
            ->search($request->search)
            ->faculty($request->facultyIds)
            ->role($request->roles)
            ->with('faculty')
            ->orderBy('created_at', 'desc')
            ->paginate(Constants::PER_PAGE);

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        if (!Auth::guard('api')->user()->can('view', $user)) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        return new UserResource($user);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        return new UserResource(User::create($data));
    }

    /**
     * Reset user password to default 'password' and set is_change_password to false
     */
    public function resetPassword(User $user): JsonResponse
    {
        try {
            if (!Auth::guard('api')->user() && !Auth::user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Check if current user is super admin or belongs to same faculty as target user
            $currentUser = Auth::guard('api')->user() ?? Auth::user();
            if (!$currentUser->isSuperAdmin() && $currentUser->faculty_id !== $user->faculty_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                ], 403);
            }

            $user->update([
                'password' => Hash::make('password'),
                'is_change_password' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mật khẩu đã được reset thành công!',
            ]);
        } catch (Throwable $th) {
            Log::error('Reset password error: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi reset mật khẩu!',
            ], 500);
        }
    }
}
