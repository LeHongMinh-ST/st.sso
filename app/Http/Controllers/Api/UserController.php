<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Helpers\Constants;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::guard('api')->user()->isSuperAdmin()) {
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
        if (!Auth::guard('api')->user()->isSuperAdmin() && $user->faculty_id !== Auth::guard('api')->user()->faculty_id) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        if (Role::Student === Auth::guard('api')->user()->role) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        return new UserResource($user);
    }
}
