<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

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

    /* =======================
        LISTAR
    ======================= */
    public function index()
    {
        return Product::with(['category', 'subcategory'])->get();
    }

    /* =======================
        CREAR
    ======================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_product'   => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id_category',
            'subcategory_id' => 'required|exists:subcategories,id_subcategory',
        ]);

        $product = Product::create([
            'name_product'   => $validated['name_product'],
            'category_id'    => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'state_product'  => 1,
            'created_by'     => Auth::user()->id_user,
        ]);

        return response()->json([
            'message' => 'Producto creado correctamente',
            'product' => $product
        ], 201);
    }

    /* =======================
        MOSTRAR
    ======================= */
    public function show($id)
    {
        return Product::with(['category', 'subcategory'])
            ->findOrFail($id);
    }

    /* =======================
        ACTUALIZAR
    ======================= */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name_product'   => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id_category',
            'subcategory_id' => 'required|exists:subcategories,id_subcategory',
            'state_product'  => 'required|in:0,1',
        ]);

        $product = Product::findOrFail($id);

        $product->update([
            'name_product'   => $validated['name_product'],
            'category_id'    => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'state_product'  => $validated['state_product'],
            'updated_by'     => Auth::user()->id_user,
        ]);

        return response()->json([
            'message' => 'Producto actualizado correctamente'
        ]);
    }

    /* =======================
        ACTIVAR / DESACTIVAR
    ======================= */
    public function toggle($id)
    {
        $product = Product::findOrFail($id);

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
            'state_product' => !$product->state_product,
            'updated_by'    => Auth::user()->id_user,
            'deleted_by'    => $product->state_product ? Auth::user()->id_user : null
        ]);

        return response()->json([
            'message' => 'Estado del producto actualizado'
        ]);
    }

    /* =======================
        BORRADO LÓGICO
    ======================= */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'state_product' => 0,
            'deleted_by'    => Auth::user()->id_user,
        ]);

        return response()->json([
            'message' => 'Producto desactivado correctamente'
        ]);
    }
}
