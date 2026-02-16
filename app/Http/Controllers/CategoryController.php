<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_categories', only: ['index']),
            new Middleware('permission:create_categories', only: ['store']),
            new Middleware('permission:update_categories', only: ['update', 'toggle']),
            new Middleware('permission:delete_categories', only: ['destroy']),
        ];
    }

    public function index()
    {
        return Category::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name_category' => 'required|unique:categories']);
        $validated['created_by'] = Auth::id();
        $category = Category::create($validated);
        return response()->json($category, 201);
    }


    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name_category' => "required|string|max:255|unique:categories,name_category,{$id},id_category",
            'state_category' => 'nullable|boolean'
        ]);

        $category->update([
            'name_category'  => $validated['name_category'],
            'state_category' => $request->has('state_category') ? $validated['state_category'] : $category->state_category,
            'updated_by'     => Auth::id()
        ]);

        return response()->json(['message' => 'Categoría actualizada', 'category' => $category]);
    }

    public function toggle($id)
    {
        return DB::transaction(function () use ($id) {
            $category = Category::findOrFail($id);
            $userId = Auth::id();
            $newState = $category->state_category ? 0 : 1;

            $category->update([
                'state_category' => $newState,
                'updated_by' => $userId,
                'deleted_by' => $newState ? null : $userId,
            ]);

            if (!$newState) {
                Subcategory::where('category_id', $id)->update(['state_subcategory' => 0, 'updated_by' => $userId, 'deleted_by' => $userId]);
                Product::where('category_id', $id)->update(['state_product' => 0, 'updated_by' => $userId, 'deleted_by' => $userId]);
            }
            return response()->json(['message' => 'Estado actualizado en cascada']);
        });
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['message' => 'Categoría eliminada físicamente']);
    }
}
