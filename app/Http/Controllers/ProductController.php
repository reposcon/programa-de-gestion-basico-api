<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::where('state_product', true)->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_product' => 'required|string',
            'category_id' => 'required|exists:categories,id_category',
            'subcategory_id' => 'required|exists:subcategories,id_subcategory',
        ]);
        $validated['state_product'] = true; // Por defecto, el producto está activo
        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->state_product = false; // Borrado lógico
        $product->save();

        return response()->json(['message' => 'Producto desactivado correctamente']);
    }
}
