<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'name_user' => 'required|string',
            'password_user' => 'required|string'
        ]);

        $credentials = [
            'name_user' => $request->name_user, // debe coincidir con nombre columna en la BD
            'password' => $request->password_user,  // la clave siempre debe llamarse 'password'
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $user = Auth::user();

        if ($user->state === 0) {
            Auth::logout();
            return response()->json(['message' => 'Usuario inactivo'], 403);
        }

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada']);
    }
}
