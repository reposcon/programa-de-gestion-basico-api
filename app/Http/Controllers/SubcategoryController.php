<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function index()
    {
        return Subcategory::withCount('products')->get();
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
        $subcategory = Subcategory::findOrFail($id);
        return response()->json($subcategory);
    }

    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->update($request->all());

        // Si se desactiva la subcategoría
        if ($request->has('state_subcategory') && $request->state_subcategory == 0) {
            \App\Models\Product::where('subcategory_id', $subcategory->id_subcategory)
                ->update(['state_product' => 0]);
        }

        return response()->json(['message' => 'Subcategoría actualizada correctamente']);
    }


    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->state_subcategory = false; // Marcamos como inactiva
        $subcategory->save();

        return response()->json(['message' => 'Subcategoría desactivada correctamente']);
    }
}
