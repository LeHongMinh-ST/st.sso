<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Passport\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateSSOController extends Controller
{
    public function loginSSO(Request $request)
    {
        $clientId = $request->query('client_id');

        $client = Client::where('client_id', $clientId)->first();
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $redirect = $client->redirect_uri;

        if (!Auth::check()) {
            return redirect('/login?redirect=' . urlencode($redirect));
        }
    
        $user = Auth::user();
        $token = $user->createToken('SSO Token')->accessToken;
        
        // Chuyển hướng về Hệ thống B với token
        return redirect($redirect . '?sso_token=' . $token . '&client_id=' . $client->client_id);
    }
}
