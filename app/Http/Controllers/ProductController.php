<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Imports\ProductsImport;
use App\Exports\ProductTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_products', only: ['index']),
            new Middleware('permission:create_products', only: ['store', 'importExcel' , 'exportExcel' ]),
            new Middleware('permission:update_products', only: ['update', 'toggle']),
            new Middleware('permission:delete_products', only: ['destroy']),
        ];
    }

    public function index()
    {
        return Product::with(['category', 'subcategory', 'tax'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_product'    => 'required|string|max:255',
            'price_cost'      => 'required|numeric|min:0',
            'tax_id'          => 'required|exists:tax_settings,id_tax',
            'price_sell'      => 'required|numeric|min:0',
            'is_tax_included' => 'required|boolean',
            'category_id'     => 'required|exists:categories,id_category',
            'subcategory_id'  => 'required|exists:subcategories,id_subcategory',
            'stock'           => 'nullable|integer|min:0' 
        ]);

        $tax = DB::table('tax_settings')->where('id_tax', $validated['tax_id'])->first();
        $rate = $tax->tax_rate / 100;

        // Lógica de Precios
        if ($validated['is_tax_included']) {
            $validated['price_net'] = $validated['price_sell'] / (1 + $rate);
        } else {
            $validated['price_net'] = $validated['price_sell'];
            $validated['price_sell'] = $validated['price_net'] * (1 + $rate);
        }

        $validated['stock'] = $request->input('stock', 0);
        $validated['created_by'] = Auth::id();

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Producto creado con éxito',
            'product' => $product
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name_product'    => 'required|string|max:255',
            'price_cost'      => 'required|numeric|min:0',
            'tax_id'          => 'required|exists:tax_settings,id_tax',
            'price_sell'      => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0', // 👈 Ahora sí validamos y usamos el stock
            'is_tax_included' => 'required|boolean',
            'category_id'     => 'required|exists:categories,id_category',
            'subcategory_id'  => 'required|exists:subcategories,id_subcategory',
        ]);

        $tax = DB::table('tax_settings')->where('id_tax', $validated['tax_id'])->first();
        $rate = $tax->tax_rate / 100;

        if ($validated['is_tax_included']) {
            $validated['price_net'] = $validated['price_sell'] / (1 + $rate);
        } else {
            $validated['price_net'] = $validated['price_sell'];
            $validated['price_sell'] = $validated['price_net'] * (1 + $rate);
        }

        // Incluimos todos los campos en el update, incluido el stock
        $product->update(array_merge($validated, [
            'updated_by' => Auth::id()
        ]));

        return response()->json([
            'message' => 'Producto actualizado',
            'product' => $product->load('tax')
        ]);
    }

    public function toggle($id)
    {
        $product = Product::findOrFail($id);
        $newState = $product->state_product ? 0 : 1;

        if ($newState === 1) {
            $sub = Subcategory::where('id_subcategory', $product->subcategory_id)->where('state_subcategory', 1)->exists();
            if (!$sub) return response()->json(['message' => 'No se puede activar: Subcategoría inactiva'], 422);
        }

        $product->update([
            'state_product' => $newState,
            'updated_by' => Auth::id()
        ]);

        return response()->json(['message' => 'Estado del producto actualizado']);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Producto eliminado permanentemente']);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // Máximo 5MB
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return response()->json(['status' => 'success', 'message' => 'Productos cargados con éxito']);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return response()->json(['status' => 'error', 'message' => 'Error en los datos del Excel', 'errors' => $failures], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error en la carga: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProductTemplateExport, 'plantilla_productos.xlsx');
    }
}
