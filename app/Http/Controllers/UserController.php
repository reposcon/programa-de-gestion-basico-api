<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role' => function($query) {
            $query->where('state_role', 1);
        }])->get();
        
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_user' => 'required|unique:users,name_user',
            'password_user' => 'required|min:6',
            'role_id' => 'required|exists:roles,id_role'
        ]);

        $role = \App\Models\Role::find($request->role_id);
        if (!$role || $role->state_role == 0) {
            return response()->json(['message' => 'No puedes asignar un rol inactivo'], 422);
        }

        $user = User::create([
            'name_user' => $request->name_user,
            'password_user' => Hash::make($request->password_user),
            'role_id' => $request->role_id,
            'state_user' => 1 
        ]);

        return response()->json(['message' => 'Usuario creado', 'user' => $user], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name_user' => "required|unique:users,name_user,{$id},id_user",
            'role_id' => 'required|exists:roles,id_role',
            'state_user' => 'required|in:0,1' 
        ]);

        $role = \App\Models\Role::find($request->role_id);
        if ($role->state_role == 0) {
            return response()->json(['message' => 'El rol seleccionado está inactivo y no puede ser asignado'], 422);
        }

        $user->name_user = $request->name_user;
        $user->role_id = $request->role_id;
        $user->state_user = $request->state_user;

        if ($request->filled('password_user')) {
            $user->password_user = Hash::make($request->password_user);
        }

        $user->save();
        return response()->json(['message' => 'Usuario actualizado', 'user' => $user->load('role')]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->state_user = 0;
        $user->save();

        return response()->json(['message' => 'Usuario desactivado correctamente']);
    }
}