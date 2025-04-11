<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class UserRoleController extends Controller
{
    public function edit(User $user): View|Application|Factory|RedirectResponse
    {
        return view('pages.user.roles', compact('user'));
    }
}
