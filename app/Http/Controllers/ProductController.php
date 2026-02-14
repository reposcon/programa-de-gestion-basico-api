<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_products', only: ['index', 'show']),
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
            'name_product'   => 'required|string',
            'category_id'      => 'required|exists:categories,id_category',
            'subcategory_id'   => 'required|exists:subcategories,id_subcategory',
            'state_product'    => 'nullable|integer|in:0,1'
        ]);

        $validated['state_product'] = $validated['state_product'] ?? 1;

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'subcategory'])->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name_product' => $request->name_product,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'state_product' => $request->state_product
        ]);

        return response()->json(['message' => 'Producto actualizado correctamente']);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->state_product = 0;
        $product->save();

        return response()->json(['message' => 'Producto desactivado correctamente']);
    }

    public function toggle($id)
    {
        $product = Product::findOrFail($id);

        // Si se intenta activar (pasar de 0 a 1), validamos padres
        if ($product->state_product == 0) {
            $categoryActive = Category::where('id_category', $product->category_id)
                ->where('state_category', 1)
                ->exists();

            $subcategoryActive = Subcategory::where('id_subcategory', $product->subcategory_id)
                ->where('state_subcategory', 1)
                ->exists();

            if (!$categoryActive || !$subcategoryActive) {
                return response()->json([
                    'message' => 'No se puede activar el producto porque su categoría o subcategoría está inactiva'
                ], 422);
            }
        }

        $product->update([
            'state_product' => $product->state_product ? 0 : 1
        ]);

        return response()->json(['message' => 'Estado del producto actualizado']);
    }
}
