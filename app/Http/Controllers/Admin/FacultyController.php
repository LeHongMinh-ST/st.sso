<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class FacultyController extends Controller
{
    public function index(): View|Application|Factory|RedirectResponse
    {
        return view('pages.faculty.index');
    }

    public function create(): View|Application|Factory|RedirectResponse
    {
        return view('pages.faculty.create');
    }

    public function show(Faculty $faculty): View|Application|Factory|RedirectResponse
    {
        return view('pages.faculty.detail', compact('faculty'));
    }

    public function edit(Faculty $faculty): View|Application|Factory|RedirectResponse
    {
        return view('pages.faculty.edit', compact('faculty'));
    }

}
