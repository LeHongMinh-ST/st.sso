<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOAuthClientRequest;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Client;
use Symfony\Component\HttpFoundation\Response;

class OAuthClientController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'clients' => Client::where('personal_access_client', false)
                ->where('password_client', false)
                ->get()
        ], Response::HTTP_OK);
    }

    public function store(StoreOAuthClientRequest $request): JsonResponse
    {
        $client = new Client();
        $client->name = $request->name;
        $client->redirect = $request->redirect;
        $client->website = $request->website;
        $client->description = $request->description;
        $client->user_id = auth()->id();
        $client->save();

        return response()->json($client, Response::HTTP_CREATED);
    }
}