<?php

namespace Alpha\Oauth\Http\Controllers;

use Illuminate\Http\Request;
use Alpha\Oauth\Facades\SocialAuth;

class SocialAuthController extends Controller
{
    public function handleAuth(Request $request, string $provider)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->input('token');

        $userInfo = SocialAuth::handleSocialAuth($provider, $token);

        return response()->json([
            'message' => 'Authentication successful',
            'user' => $userInfo,
        ]);
    }
}