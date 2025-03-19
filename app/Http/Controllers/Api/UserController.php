<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Constants;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
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
        return new UserResource($user);
    }
}
