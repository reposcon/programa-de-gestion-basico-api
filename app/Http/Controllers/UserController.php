<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
     
        return User::all(); // Deja esto si quieres ver todos (activos e inactivos)
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_user' => 'required|string',
            'password_user' => 'required|string',
            'rol' => 'required|in:admin,basico',
            'state' => 'boolean'
        ]);

        $user = User::create([
            'name_user' => $request->name_user,
            'password_user' => Hash::make($request->password_user),
            'rol' => $request->rol,
            'state' => $request->state ?? true,
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name_user' => 'string',
            'password_user' => 'string',
            'rol' => 'in:admin,basico',
            'state' => 'boolean'
        ]);

        $user->name_user = $request->name_user ?? $user->name_user;

        if ($request->password_user) {
            $user->password_user = Hash::make($request->password_user);
        }

        $user->rol = $request->rol ?? $user->rol;
        $user->state = $request->state ?? $user->state;

        $user->save();

        return response()->json($user, 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->state = false; 
        $user->save();

        return response()->json(['message' => 'Usuario desactivado (borrado lógico)']);
    }
}
