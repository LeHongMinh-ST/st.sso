<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    //
    public function index()
    {
        return view('pages.client.index');
    }

    public function create()
    {
        return view('pages.client.create');
    }

    public function show(Client $client)
    {
        return view('pages.client.detail', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('pages.client.edit', compact('client'));
    }
}
