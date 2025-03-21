<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function index(): View|Application|Factory|RedirectResponse
    {
        return view('pages.client.index');
    }

    public function create(): View|Application|Factory|RedirectResponse
    {
        return view('pages.client.create');
    }

    public function show(Client $client): View|Application|Factory|RedirectResponse
    {
        return view('pages.client.detail', compact('client'));
    }

    public function edit(Client $client): View|Application|Factory|RedirectResponse
    {
        return view('pages.client.edit', compact('client'));
    }
}
