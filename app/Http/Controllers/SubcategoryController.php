<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubcategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_subcategories', only: ['index']),
            new Middleware('permission:create_subcategories', only: ['store']),
            new Middleware('permission:update_subcategories', only: ['update', 'toggle']),
            new Middleware('permission:delete_subcategories', only: ['destroy']),
        ];
    }

    public function index()
    {
        return Subcategory::with('category')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_subcategory' => 'required',
            'category_id' => 'required|exists:categories,id_category',
            'default_tax_id' => 'nullable|exists:tax_settings,id_tax'
        ]);
        $validated['created_by'] = Auth::id();
        $subcategory = Subcategory::create($validated);
        return response()->json($subcategory, 201);
    }

    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $validated = $request->validate([
            'name_subcategory' => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id_category',
            'default_tax_id' => 'nullable|exists:tax_settings,id_tax'
        ]);

        $subcategory->update([
            'name_subcategory'  => $validated['name_subcategory'],
            'category_id'       => $validated['category_id'],
            'default_tax_id' => $validated['default_tax_id'],
            'updated_by'        => Auth::id()
        ]);

        return response()->json(['message' => 'Subcategoría actualizada', 'subcategory' => $subcategory]);
    }

    public function toggle($id)
    {
        return DB::transaction(function () use ($id) {
            $sub = Subcategory::findOrFail($id);
            $userId = Auth::id();
            $newState = $sub->state_subcategory ? 0 : 1;

            if ($newState === 1) {
                $parent = Category::where('id_category', $sub->category_id)->where('state_category', 1)->exists();
                if (!$parent) return response()->json(['message' => 'Categoría padre inactiva'], 422);
            }

            $sub->update([
                'state_subcategory' => $newState,
                'updated_by' => $userId,
                'deleted_by' => $newState ? null : $userId
            ]);

            if (!$newState) {
                Product::where('subcategory_id', $id)->update(['state_product' => 0, 'updated_by' => $userId, 'deleted_by' => $userId]);
            }
            return response()->json(['message' => 'Subcategoría actualizada']);
        });
    }

    public function destroy($id)
    {
        Subcategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Borrado físico']);
    }
}
