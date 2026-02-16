<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_categories',   only: ['index', 'show']),
            new Middleware('permission:create_categories', only: ['store']),
            new Middleware('permission:update_categories', only: ['update', 'toggle']),
            new Middleware('permission:delete_categories', only: ['destroy']),
        ];
    }

    /* =======================
        LISTAR
    ======================= */
    public function index()
    {
        return Category::all(); // incluye activas e inactivas
    }

    /* =======================
        MOSTRAR
    ======================= */
    public function show($id)
    {
        return Category::findOrFail($id);
    }

    /* =======================
        CREAR
    ======================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_category'  => 'required|string|max:255',
            'state_category' => 'nullable|boolean'
        ]);

        $category = Category::create([
            'name_category'  => $validated['name_category'],
            'state_category' => $validated['state_category'] ?? 1,
            'created_by'     => Auth::user()->id_user,
        ]);

        return response()->json([
            'message'  => 'Categoría creada correctamente',
            'category' => $category
        ], 201);
    }

    /* =======================
        ACTUALIZAR
    ======================= */
    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $validated = $request->validate([
                'name_category'  => 'sometimes|string|max:255',
                'state_category' => 'sometimes|boolean'
            ]);

            $category = Category::findOrFail($id);
            $oldState = $category->state_category;

            $category->update([
                ...$validated,
                'updated_by' => Auth::user()->id_user
            ]);

            // si cambia el estado → cascada
            if (array_key_exists('state_category', $validated) && $oldState != $validated['state_category']) {
                Subcategory::where('category_id', $id)
                    ->update(['state_subcategory' => $validated['state_category']]);

                Product::where('category_id', $id)
                    ->update(['state_product' => $validated['state_category']]);
            }
        });

        return response()->json([
            'message' => 'Categoría actualizada correctamente'
        ]);
    }

    /* =======================
        ACTIVAR / DESACTIVAR
    ======================= */
    public function toggle($id)
    {
        DB::transaction(function () use ($id) {

            $category = Category::findOrFail($id);
            $newState = !$category->state_category;

            $category->update([
                'state_category' => $newState,
                'updated_by'     => Auth::user()->id_user,
                'deleted_by'     => $newState ? null : Auth::user()->id_user,
            ]);

            // si se desactiva → cascada
            if (!$newState) {
                Subcategory::where('category_id', $id)
                    ->update(['state_subcategory' => 0]);

                Product::where('category_id', $id)
                    ->update(['state_product' => 0]);
            }
        });

        return response()->json([
            'message' => 'Estado de la categoría actualizado'
        ]);
    }

    /* =======================
        DESACTIVAR 
    ======================= */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        DB::transaction(function () use ($category) {

            $category->update([
                'state_category' => 0,
                'deleted_by'     => Auth::user()->id_user,
            ]);

            Subcategory::where('category_id', $category->id_category)
                ->update(['state_subcategory' => 0]);

            Product::where('category_id', $category->id_category)
                ->update(['state_product' => 0]);
        });

        return response()->json([
            'message' => 'Categoría desactivada correctamente'
        ]);
    }
}
