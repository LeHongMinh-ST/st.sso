<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $clients = Client::query()->where('is_show_dashboard', true)->get();
        return view('pages.dashboard', compact('clients'));
    }
}
