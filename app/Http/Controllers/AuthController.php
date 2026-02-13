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

        $user = User::with('role')->where('name_user', $request->name_user)->first();

        if (!$user || !Hash::check($request->password_user, $user->password_user)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if ($user->state === 0 || !$user->role || $user->role->state_role === 0) {
            $why = $user->state === 0 ? 'Usuario inactivo' : 'El rol asignado está desactivado';
            return response()->json([
                'message' => 'Acceso denegado: ' . $why
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id_user' => $user->id_user,
                'name_user' => $user->name_user,
                'name_role' => $user->role->name_role,
                'id_role' => $user->role->id_role,
                'state_user' => $user->state_user
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada y token revocado'
        ]);
    }
}