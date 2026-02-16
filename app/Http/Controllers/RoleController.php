<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_roles', only: ['index']),
            new Middleware('permission:create_roles', only: ['store']),
            new Middleware('permission:update_roles', only: ['update', 'toggle']),
            new Middleware('permission:delete_roles', only: ['destroy']),
        ];
    }

    public function index() { return Role::withCount('users')->get(); }

    public function store(Request $request)
    {
        $validated = $request->validate(['name_role' => 'required|unique:roles,name_role']);
        $validated['created_by'] = Auth::id();
        $role = Role::create($validated);
        return response()->json($role, 201);
    }

    public function toggle($id)
    {
        $role = Role::findOrFail($id);
        $newState = $role->state_role ? 0 : 1;
        $role->update([
            'state_role' => $newState,
            'updated_by' => Auth::id(),
            'deleted_by' => $newState ? null : Auth::id()
        ]);
        return response()->json(['message' => 'Estado del rol actualizado']);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->users()->exists()) {
            return response()->json(['message' => 'No se puede eliminar un rol con usuarios'], 422);
        }
        $role->permissions()->detach();
        $role->delete();
        return response()->json(['message' => 'Rol eliminado físicamente']);
    }
}