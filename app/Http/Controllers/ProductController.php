<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_products', only: ['index']),
            new Middleware('permission:create_products', only: ['store']),
            new Middleware('permission:update_products', only: ['update', 'toggle']),
            new Middleware('permission:delete_products', only: ['destroy']),
        ];
    }

    public function index()
    {
        return Product::with(['category', 'subcategory'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_product' => 'required',
            'category_id' => 'required|exists:categories,id_category',
            'subcategory_id' => 'required|exists:subcategories,id_subcategory'
        ]);
        $validated['created_by'] = Auth::id();
        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name_product'   => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id_category',
            'subcategory_id' => 'required|exists:subcategories,id_subcategory',
            'state_product'  => 'nullable|boolean'
        ]);

        $product->update([
            'name_product'   => $validated['name_product'],
            'category_id'    => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'state_product'  => $request->has('state_product') ? $validated['state_product'] : $product->state_product,
            'updated_by'     => Auth::id()
        ]);

        return response()->json(['message' => 'Producto actualizado', 'product' => $product]);
    }

    public function toggle($id)
    {
        $product = Product::findOrFail($id);
        $newState = $product->state_product ? 0 : 1;

        if ($newState === 1) {
            $sub = Subcategory::where('id_subcategory', $product->subcategory_id)->where('state_subcategory', 1)->exists();
            if (!$sub) return response()->json(['message' => 'Subcategoría inactiva'], 422);
        }

        $product->update([
            'state_product' => $newState,
            'updated_by' => Auth::id(),
            'deleted_by' => $newState ? null : Auth::id()
        ]);
        return response()->json(['message' => 'Producto actualizado']);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Eliminado permanentemente']);
    }
}
