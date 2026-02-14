<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_users',   only: ['index']),
            new Middleware('permission:create_users', only: ['store']),
            new Middleware('permission:update_users', only: ['update']), // Corregido según tu DB
            new Middleware('permission:delete_users', only: ['destroy']),
        ];
    }

    public function index()
    {
        return User::with(['roles' => function ($query) {
            $query->where('state_role', 1);
        }])->get()->map(function ($user) {
            $user->role = $user->roles->first();
            return $user;
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_user'     => 'required|unique:users,name_user',
            'password_user' => 'required|min:6',
            'id_role'       => 'required|exists:roles,id_role'
        ]);

        $role = Role::find($request->id_role);
        if (!$role || $role->state_role == 0) {
            return response()->json(['message' => 'No puedes asignar un rol inactivo'], 422);
        }

        $user = User::create([
            'name_user'     => $request->name_user,
            'password_user' => Hash::make($request->password_user),
            'state_user'    => 1
        ]);

        $user->roles()->attach($request->id_role);

        return response()->json([
            'message' => 'Usuario creado',
            'user'    => $user->load('roles')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Quitamos el 'required'. Ahora solo validará si el campo está presente.
        $request->validate([
            'name_user'     => "sometimes|unique:users,name_user,{$id},id_user",
            'id_role'       => 'sometimes|exists:roles,id_role',
            'state_user'    => 'sometimes|in:0,1',
            'password_user' => 'nullable|min:6'
        ]);

        // Actualizamos solo si los campos vienen en la petición
        if ($request->has('name_user')) {
            $user->name_user = $request->name_user;
        }

        if ($request->has('state_user')) {
            $user->state_user = $request->state_user;
        }

        // Lógica de contraseña: solo si tiene contenido real
        if ($request->filled('password_user')) {
            $user->password_user = Hash::make($request->password_user);
        }

        $user->save();

        // Actualizamos el rol solo si se envió id_role
        if ($request->has('id_role')) {
            $user->roles()->sync([$request->id_role]);
        }

        // Devolvemos el usuario con su rol para que Angular actualice la tabla
        $user->load('roles');
        $user->role = $user->roles->first();
        $user->id_role = $user->role ? $user->role->id_role : null;

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user
        ]);
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->state_user = 0;
        $user->save();

        return response()->json(['message' => 'Usuario desactivado correctamente']);
    }
}
