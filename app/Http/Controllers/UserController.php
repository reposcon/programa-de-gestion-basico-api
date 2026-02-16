<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_users',   only: ['index']),
            new Middleware('permission:create_users', only: ['store']),
            new Middleware('permission:update_users', only: ['update', 'toggle']),
            new Middleware('permission:delete_users', only: ['destroy']),
        ];
    }

    /* =======================
        LISTAR
    ======================= */
    public function index()
    {
        return User::with(['roles'])->get()->map(function ($user) {
            $user->role = $user->roles->first();
            return $user;
        });
    }

    /* =======================
        CREAR
    ======================= */
    public function store(Request $request)
    {
        $request->validate([
            'name_user'     => 'required|unique:users,name_user',
            'password_user' => 'required|min:6',
            'id_role'       => 'required|exists:roles,id_role'
        ]);

        $role = Role::find($request->id_role);
        if ($role->state_role == 0) {
            return response()->json([
                'message' => 'No puedes asignar un rol inactivo'
            ], 422);
        }

        $user = User::create([
            'name_user'     => $request->name_user,
            'password_user' => Hash::make($request->password_user),
            'state_user'    => 1,
            'created_by'    => Auth::user()->id_user,
        ]);

        $user->roles()->attach($request->id_role);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user' => $user->load('roles')
        ], 201);
    }

    /* =======================
        ACTUALIZAR
    ======================= */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name_user'     => "sometimes|unique:users,name_user,{$id},id_user",
            'id_role'       => 'sometimes|exists:roles,id_role',
            'state_user'    => 'sometimes|in:0,1',
            'password_user' => 'nullable|min:6'
        ]);

        if ($request->has('name_user')) {
            $user->name_user = $request->name_user;
        }

        if ($request->has('state_user')) {
            $user->state_user = $request->state_user;
        }

        if ($request->filled('password_user')) {
            $user->password_user = Hash::make($request->password_user);
        }

        $user->updated_by = Auth::user()->id_user;
        $user->save();

        if ($request->has('id_role')) {
            $user->roles()->sync([$request->id_role]);
        }

        $user->load('roles');
        $user->role = $user->roles->first();

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user
        ]);
    }

    /* =======================
        ACTIVAR / DESACTIVAR
    ======================= */
    public function toggle($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'state_user' => !$user->state_user,
            'updated_by' => Auth::user()->id_user,
            'deleted_by' => $user->state_user ? Auth::user()->id_user : null
        ]);

        return response()->json([
            'message' => 'Estado del usuario actualizado'
        ]);
    }

    /* =======================
        DESACTIVAR 
    ======================= */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'state_user' => 0,
            'deleted_by' => Auth::user()->id_user
        ]);

        return response()->json([
            'message' => 'Usuario desactivado correctamente'
        ]);
    }
}
