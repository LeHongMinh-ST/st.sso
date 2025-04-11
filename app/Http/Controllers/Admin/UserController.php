<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(): View|Application|Factory|RedirectResponse
    {
        return view('pages.user.index');
    }

    public function create(): View|Application|Factory|RedirectResponse
    {
        return view('pages.user.create');
    }

    public function edit(User $user): View|Application|Factory|RedirectResponse
    {
        $user->load('faculty');

        return view('pages.user.edit', compact('user'));
    }

    public function show(User $user): View|Application|Factory|RedirectResponse
    {
        $user->load('faculty');

        return view('pages.user.detail', compact('user'));
    }
}
