<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_category' => 'required|string',
            'state_category' => 'boolean'
        ]);

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

   public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);
    $category->update($request->all());

    if ($request->has('state_category') && $request->state_category == 0) {
        \App\Models\Subcategory::where('category_id', $category->id_category)
            ->update(['state_subcategory' => 0]);
        \App\Models\Product::where('category_id', $category->id_category)
            ->update(['state_product' => 0]);
    }

    return response()->json(['message' => 'Categoría actualizada correctamente']);
}

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->state_category = false;
        $category->save();

        return response()->json(['message' => 'Categoría desactivada']);
    }
}
