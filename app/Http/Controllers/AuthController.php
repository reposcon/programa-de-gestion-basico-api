<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
// IMPORTACIONES PARA LARAVEL 11
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuthController extends Controller implements HasMiddleware
{
    /**
     * Definimos los middlewares del controlador
     */
    public static function middleware(): array
    {
        return [
            // El login es público, pero el logout requiere estar autenticado
            new Middleware('auth:sanctum', only: ['logout']),
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'name_user' => 'required|string',
            'password_user' => 'required|string'
        ]);

        // Cargar roles y permisos
        $user = User::with('roles.permissions')
            ->where('name_user', $request->name_user)
            ->first();

        if (! $user || ! Hash::check($request->password_user, $user->password_user)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Usuario inactivo
        if ($user->state_user === 0) {
            return response()->json([
                'message' => 'Usuario inactivo'
            ], 403);
        }

        // Validar que tenga al menos un rol activo
        $activeRoles = $user->roles->where('state_role', 1);

        if ($activeRoles->isEmpty()) {
            return response()->json([
                'message' => 'No tienes roles activos asignados'
            ], 403);
        }

        // Crear token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Extraer permisos únicos
        $permissions = $activeRoles
            ->flatMap->permissions
            ->pluck('name_permission')
            ->unique()
            ->values();

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id_user' => $user->id_user,
                'name_user' => $user->name_user,
                'state_user' => $user->state_user,
                'id_role' => $activeRoles->pluck('id_role'),
                'name_role' => $activeRoles->pluck('name_role'),
                'permissions' => $permissions
            ]
        ]);
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada'
        ]);
    }
}