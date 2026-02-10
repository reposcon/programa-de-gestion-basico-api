<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_product'   => 'required|string',
            'category_id'      => 'required|exists:categories,id_category',
            'subcategory_id'   => 'required|exists:subcategories,id_subcategory',
            'state_product'    => 'nullable|integer|in:0,1' // Nuevo campo
        ]);

        // Si no viene el estado, por defecto será activo
        $validated['state_product'] = $validated['state_product'] ?? 1;

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

        $validated = $request->validate([
            'nombre_product'   => 'sometimes|string',
            'category_id'      => 'sometimes|exists:categories,id_category',
            'subcategory_id'   => 'sometimes|exists:subcategories,id_subcategory',
            'state_product'    => 'nullable|integer|in:0,1' // Validar estado
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Borrado lógico: poner estado en 0 (inactivo)
        $product->state_product = 0;
        $product->save();

        return response()->json(['message' => 'Producto desactivado correctamente']);
    }
}
