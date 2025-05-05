<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class UserRoleController extends Controller
{
    public function edit(User $user): View|Application|Factory|RedirectResponse
    {
        if (Gate::denies('viewAny', Role::class)) {
            abort(403, 'Bạn không có quyền quản lý vai trò người dùng.');
        }

        return view('pages.user.roles', compact('user'));
    }
}
