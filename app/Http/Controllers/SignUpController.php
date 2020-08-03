<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Events\Registered;

class SignUpController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::create($request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]));

        $user->api_token = $user->createToken($request->input('device_name', ''))->plainTextToken;

        return (new UserResource($user))->response()
            ->setStatusCode(201);

        return new UserResource($user);
    }
}
