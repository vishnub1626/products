<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Auth\AuthenticationException;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $authenticated = auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        if (!$authenticated) {
            throw new AuthenticationException("Invalid credentials.");
        }

        $user = auth()->user();
        $user->api_token = $user->createToken($request->input('device_name', ''))->plainTextToken;

        return new UserResource($user);
    }
}
