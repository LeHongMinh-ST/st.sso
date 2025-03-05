<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOAuthClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\Client;
use Symfony\Component\HttpFoundation\Response;

class OAuthClientController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'clients' => Client::where('personal_access_client', false)
                ->where('password_client', false)
                ->get(),
        ], Response::HTTP_OK);
    }

    public function store(StoreOAuthClientRequest $request): JsonResponse
    {
        $client = new Client;
        $client->name = $request->name;
        $client->redirect = $request->redirect;
        $client->website = $request->website;
        $client->description = $request->description;
        $client->user_id = auth()->id();
        $client->save();

        return response()->json($client, Response::HTTP_CREATED);
    }

    public function loginSSO(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login', ['redirect' => $request->query('redirect')]);
        }

        $user = auth()->user();
        $token = $user->createToken('SSO Token')->accessToken;

        return redirect($request->query('redirect').'?sso_token='.$token);
    }
}
