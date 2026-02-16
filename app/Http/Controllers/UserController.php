<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_users', only: ['index']),
            new Middleware('permission:create_users', only: ['store']),
            new Middleware('permission:update_users', only: ['update', 'toggle']),
            new Middleware('permission:delete_users', only: ['destroy']),
        ];
    }

    public function index()
    {
        return User::with('roles')->get()->map(function ($user) {
            $user->role = $user->roles->first();
            return $user;
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_user' => 'required|unique:users,name_user',
            'password_user' => 'required|min:6',
            'id_role' => 'required|exists:roles,id_role'
        ]);

        return DB::transaction(function () use ($validated) {
            $user = User::create([
                'name_user' => $validated['name_user'],
                'password_user' => Hash::make($validated['password_user']),
                'state_user' => 1,
                'created_by' => Auth::id()
            ]);

            $user->roles()->attach($validated['id_role']);
            return response()->json(['message' => 'Usuario creado', 'user' => $user], 201);
        });
    }

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name_user'     => "required|string|max:255|unique:users,name_user,{$id},id_user",
        'password_user' => 'nullable|min:6', 
        'id_role'       => 'required|exists:roles,id_role',
        'state_user'    => 'nullable|boolean'
    ]);

    $userData = [
        'name_user'  => $validated['name_user'],
        'updated_by' => Auth::id(), 
    ];

    if ($request->filled('password_user')) {
        $userData['password_user'] = Hash::make($validated['password_user']);
    }

    if ($request->has('state_user')) {
        $userData['state_user'] = $validated['state_user'];
        if ($validated['state_user'] == 0) {
            $userData['deleted_by'] = Auth::id();
        } else {
            $userData['deleted_by'] = null;
        }
    }

    DB::transaction(function () use ($user, $userData, $validated) {
        $user->update($userData);
        
        $user->roles()->sync([$validated['id_role']]);
    });

    return response()->json([
        'message' => 'Usuario y roles actualizados correctamente',
        'user' => $user->load('roles')
    ]);
}

    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $userId = Auth::id();

        if ($user->id_user === $userId) {
            return response()->json(['message' => 'No puedes desactivarte a ti mismo'], 403);
        }

        $newState = $user->state_user ? 0 : 1;
        $user->update([
            'state_user' => $newState,
            'updated_by' => $userId,
            'deleted_by' => $newState ? null : $userId
        ]);

        return response()->json(['message' => 'Estado actualizado']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id_user === Auth::id()) {
            return response()->json(['message' => 'No puedes eliminarte a ti mismo'], 403);
        }
        $user->roles()->detach();
        $user->delete(); // Borrado físico real
        return response()->json(['message' => 'Usuario eliminado permanentemente']);
    }
}