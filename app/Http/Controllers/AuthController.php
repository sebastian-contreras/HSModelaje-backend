<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Response;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);
        $user = User::where("email", $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ResponseFormatter::error('Credenciales invalidas', 401);
        }
        $token = $user->createToken($user->role)->plainTextToken;
        return ResponseFormatter::success($token, 200);
    }
}
