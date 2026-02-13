<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
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