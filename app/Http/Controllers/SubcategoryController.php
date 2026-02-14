<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubcategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
{
    return [
        new Middleware('permission:view_subcategories', only: ['index', 'show']),
        new Middleware('permission:create_subcategories', only: ['store']),
        new Middleware('permission:update_subcategories', only: ['update', 'toggle']),
        new Middleware('permission:delete_subcategories', only: ['destroy']),
    ];
}
    public function index()
    {
        return Subcategory::with('category')->withCount('products')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_subcategory' => 'required|string',
            'state_subcategory' => 'required|boolean',
            'category_id' => 'required|exists:categories,id_category',
        ]);
        
        $subcategory = Subcategory::create($validated);
        return response()->json($subcategory, 201);
    }

    public function show($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        return response()->json($subcategory);
    }

    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $subcategory = Subcategory::findOrFail($id);
            $oldState = $subcategory->state_subcategory;

            $subcategory->update($request->all());

            // Si el estado cambia, se propaga a los productos hijos
            if (
                $request->has('state_subcategory') &&
                $oldState != $request->state_subcategory
            ) {
                Product::where('subcategory_id', $id)
                    ->update(['state_product' => $request->state_subcategory]);
            }
        });

        return response()->json([
            'message' => 'Subcategoría actualizada correctamente'
        ]);
    }

    public function toggle($id)
    {
        DB::transaction(function () use ($id) {
            $sub = Subcategory::findOrFail($id);
            $newState = $sub->state_subcategory ? 0 : 1;

            $sub->update(['state_subcategory' => $newState]);

            Product::where('subcategory_id', $id)
                ->update(['state_product' => $newState]);
        });

        return response()->json(['message' => 'Estado actualizado']);
    }
}