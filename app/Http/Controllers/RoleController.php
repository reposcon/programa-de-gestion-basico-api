<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_roles', only: ['index']),
            new Middleware('permission:create_roles', only: ['store']),
            new Middleware('permission:update_roles', only: ['update']),
            new Middleware('permission:delete_roles', only: ['destroy']),
        ];
    }
    public function index()
    {
        return response()->json(Role::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_role' => 'required|unique:roles,name_role|max:50',
        ]);

        $role = Role::create([
            'name_role' => $request->name_role,
            'state_role' => 1
        ]);

        return response()->json(['message' => 'Rol creado con éxito', 'role' => $role], 201);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name_role' => "required|unique:roles,name_role,{$id},id_role|max:50",
            'state_role' => 'required|in:0,1'
        ]);

        $role->update([
            'name_role' => $request->name_role,
            'state_role' => $request->state_role
        ]);

        return response()->json(['message' => 'Rol actualizado correctamente', 'role' => $role]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->state_role = 0;
        $role->save();

        return response()->json([
            'message' => 'Rol desactivado correctamente. Los usuarios con este rol podrían verse afectados.'
        ]);
    }
}
