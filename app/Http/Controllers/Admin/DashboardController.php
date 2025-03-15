<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $auth = Auth::user();

        $clients = cache()->remember('dashboard.clients.' . $auth->id, now()->addDays(30), function () use ($auth) {
            return Client::query()
                ->where('is_show_dashboard', true)
                ->whereJsonContains('allowed_roles', $auth->role->value)
                ->get();
        });
            
        return view('pages.dashboard', compact('clients'));
    }
}
