<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Application;

class FacultyController extends Controller
{
    //
    public function index(): View|Application|Factory|RedirectResponse
    {
        return view('pages.faculty.index');
    }
    
}
