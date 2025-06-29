<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Helpers\Constants;
use App\Http\Controllers\Controller;
use App\Http\Resources\Department\DepartmentResource;
use App\Http\Resources\Faculty\FacultyResource;
use App\Http\Resources\User\UserResource;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $faculties = Faculty::query()
            ->search($request->search)
            ->paginate(Constants::PER_PAGE);

        return FacultyResource::collection($faculties);
    }

    public function all()
    {
        $faculties = Faculty::all();
        return FacultyResource::collection($faculties);
    }

    public function getUsers(Faculty $faculty, Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user->isSuperAdmin() && $user->faculty_id !== $faculty->id) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $users = $faculty->users()
            ->search($request->get('q', ''))
            ->whereNotIn('role', [Role::SuperAdmin, Role::Student])
            ->orderBy('users.created_at', 'desc')
            ->paginate(Constants::PER_PAGE);
        return UserResource::collection($users);
    }

    public function getTeachers(Faculty $faculty, Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user->isSuperAdmin() && $user->faculty_id !== $faculty->id) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $teachers = $faculty->teachers()
            ->search($request->get('q', ''))
            ->orderBy('users.created_at', 'desc')
            ->paginate(Constants::PER_PAGE);
        return UserResource::collection($teachers);
    }

    public function getDepartments(Faculty $faculty, Request $request)
    {
        $departments = $faculty->departments()
            ->search($request->get('q', ''))
            ->orderBy('departments.created_at', 'desc')
            ->paginate(Constants::PER_PAGE);
        return DepartmentResource::collection($departments);
    }
}
