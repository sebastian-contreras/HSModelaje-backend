<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\User;
use App\Models\Usuario;
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

        $user = Usuario::where("Email", $request->email)->first();


        if (!$user || md5($request->input('password')) !== $user->Contrasena) {
            return ResponseFormatter::error('Credenciales invalidas', 401);
        }
        $token = $user->createToken($user->Rol)->plainTextToken;
        return ResponseFormatter::success([
            'token' => $token
            ,
            'user' => [
                'id' => $user->IdUsuario,
                'name' => $user->Apellidos . ', ' . $user->Nombres,
                'email' => $user->Email,
                'role' => $user->Rol,
                'token_type' => 'Bearer',
                'access_token' => $token
            ]
        ], 200);
    }
}
