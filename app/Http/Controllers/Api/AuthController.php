<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"]
        ]);

        // find the user
        $user = User::where("email", $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                "email" => "The provided credentials are incorrect."
            ]);
        }

        // check if password matches
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                "email" => "The provided credentials are incorrect."
            ]);
        }
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(
            [
                "token" => $token
            ]
        );
    }

    public function logout(Request $request)
    {

    }
}