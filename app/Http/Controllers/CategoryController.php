<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        DB::transaction(function () use ($request, $id) {

            $category = Category::findOrFail($id);

            $oldState = $category->state_category;

            $category->update($request->all());

            if (
                $request->has('state_category') &&
                $oldState != $request->state_category
            ) {
                Subcategory::where('category_id', $id)
                    ->update(['state_subcategory' => $request->state_category]);

                Product::where('category_id', $id)
                    ->update(['state_product' => $request->state_category]);
            }
        });

        return response()->json([
            'message' => 'Categoría actualizada correctamente'
        ]);
    }

    public function toggle($id)
    {
        DB::transaction(function () use ($id) {

            $category = Category::findOrFail($id);
            $newState = $category->state_category ? 0 : 1;

            $category->update(['state_category' => $newState]);

            Subcategory::where('category_id', $id)
                ->update(['state_subcategory' => $newState]);

            Product::where('category_id', $id)
                ->update(['state_product' => $newState]);
        });

        return response()->json(['message' => 'Estado actualizado']);
    }
}
