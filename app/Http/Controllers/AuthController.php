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
        return ResponseFormatter::success(['token'=>$token
    ,'user'=>[
        'id'=>$user->id,
        'name'=>$user->name,
        'email'=>$user->email,
        'role'=>$user->role,
        'token_type'=>'Bearer',
        'access_token'=>$token
    ]], 200);
    }
}
