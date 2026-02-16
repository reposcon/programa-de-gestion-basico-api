<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

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

    /* =========================
       LISTAR
    ==========================*/
    public function index()
    {
        return Subcategory::with('category')
            ->withCount('products')
            ->get();
    }

    /* =========================
       CREAR
    ==========================*/
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_subcategory'  => 'required|string',
            'state_subcategory' => 'required|boolean',
            'category_id'       => 'required|exists:categories,id_category',
        ]);

        $validated['created_by'] = Auth::user()->id_user;

        $subcategory = Subcategory::create($validated);

        return response()->json($subcategory, 201);
    }

    /* =========================
       MOSTRAR
    ==========================*/
    public function show($id)
    {
        return Subcategory::with('category')->findOrFail($id);
    }

    /* =========================
       ACTUALIZAR
    ==========================*/
    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $subcategory = Subcategory::findOrFail($id);
            $oldState = $subcategory->state_subcategory;

            $data = $request->all();
            $data['updated_by'] = Auth::user()->id_user;

            $subcategory->update($data);

            if (
                $request->has('state_subcategory') &&
                $oldState != $request->state_subcategory
            ) {
                Product::where('subcategory_id', $id)
                    ->update([
                        'state_product' => $request->state_subcategory,
                        'updated_by'    => Auth::user()->id_user,
                    ]);
            }
        });

        return response()->json([
            'message' => 'Subcategoría actualizada correctamente'
        ]);
    }

    /* =========================
       TOGGLE
    ==========================*/
    public function toggle($id)
    {
        DB::transaction(function () use ($id) {

            $sub = Subcategory::findOrFail($id);
            $newState = $sub->state_subcategory ? 0 : 1;

            if ($newState === 1) {
                $categoryActive = Category::where('id_category', $sub->category_id)
                    ->where('state_category', 1)
                    ->exists();

                if (!$categoryActive) {
                    throw new \Exception(
                        'No se puede activar la subcategoría porque la categoría está inactiva'
                    );
                }
            }

            $sub->update([
                'state_subcategory' => $newState,
                'updated_by'        => Auth::user()->id_user,
                'deleted_by'        => $newState === 0 ? Auth::user()->id_user : null,
            ]);

            if ($newState === 0) {
                Product::where('subcategory_id', $id)
                    ->update([
                        'state_product' => 0,
                        'updated_by'    => Auth::user()->id_user,
                        'deleted_by'    => Auth::user()->id_user,
                    ]);
            }
        });

        return response()->json([
            'message' => 'Estado actualizado'
        ]);
    }
}
