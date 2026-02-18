<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['logout']),
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'name_user' => 'required|string',
            'password_user' => 'required|string'
        ]);

        $user = User::with('roles.permissions')
            ->where('name_user', $request->name_user)
            ->first();

        if (!$user || !Hash::check($request->password_user, $user->password_user)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if (!$user->state_user) {
            return response()->json(['message' => 'Usuario inactivo'], 403);
        }

        $activeRoles = $user->roles->where('state_role', 1);
        if ($activeRoles->isEmpty()) {
            return response()->json(['message' => 'No tienes roles activos asignados'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $permissions = $activeRoles->flatMap->permissions->pluck('name_permission')->unique()->values();

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id_user' => $user->id_user,
                'name_user' => $user->name_user,
                'state_user' => $user->state_user,
                'id_role' => $activeRoles->first()->id_role,
                'name_role' => $activeRoles->first()->name_role,
                'permissions' => $permissions
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
}
