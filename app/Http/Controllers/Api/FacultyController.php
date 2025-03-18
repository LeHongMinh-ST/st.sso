<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Http\Resources\Faculty\FacultyResource;
use App\Helpers\Constants;
use App\Http\Resources\User\UserResource;
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

    public function getUsers(Faculty $faculty)
    {
        if(!Auth::guard('api')->user()->isSuperAdmin() && $faculty->id !== Auth::guard('api')->user()->faculty_id) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $users = $faculty->users()->paginate(Constants::PER_PAGE);
        return UserResource::collection($users);
    }
    
    public function getTeachers(Faculty $faculty)
    {
        if(!Auth::guard('api')->user()->isSuperAdmin() && $faculty->id !== Auth::guard('api')->user()->faculty_id) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $teachers = $faculty->teachers()->paginate(Constants::PER_PAGE);
        return UserResource::collection($teachers);
    }
}
