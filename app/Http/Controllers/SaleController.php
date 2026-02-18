<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Barryvdh\DomPDF\Facade\Pdf; // <--- La herramienta para crear el PDF

class SaleController extends Controller implements HasMiddleware
{
    // 1. SEGURIDAD: Quién puede ver y quién puede crear ventas
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_sales', only: ['index', 'show', 'downloadInvoice']),
            new Middleware('permission:create_sales', only: ['store']),
        ];
    }

    public function index()
    {
        $sales = Sale::with(['items.product', 'customer', 'payments.paymentMethod'])->orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $sales]);
    }

    public function store(Request $request)
    {
        // Validamos que vengan productos y pagos
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id_product',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_id' => 'nullable|exists:customers,id_customer',
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id_payment_method',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $uvt = DB::table('global_configs')->where('config_key', 'VALOR_UVT_2026')->value('config_value') ?? 52374;

            $total_sale = 0;
            $total_tax = 0;
            $items_to_save = [];

            foreach ($data['items'] as $item) {
                $product = Product::with('tax')->lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("No hay suficiente stock de {$product->name_product}");
                }

                $tax_rate = $product->tax ? $product->tax->tax_rate : 0;
                $price = $product->price_sell;

                $tax_amount = ($price - ($price / (1 + ($tax_rate / 100)))) * $item['quantity'];
                $total_item = $price * $item['quantity'];

                $total_sale += $total_item;
                $total_tax += $tax_amount;

                $items_to_save[] = [
                    'product_id' => $product->id_product,
                    'quantity' => $item['quantity'],
                    'price_at_sale' => $price,
                    'tax_rate_at_sale' => $tax_rate,
                    'tax_amount' => $tax_amount,
                    'total_item' => $total_item
                ];
            }

            $sale = Sale::create([
                'invoice_number' => $this->nextInvoice(),
                'subtotal'       => $total_sale - $total_tax,
                'total_tax'      => $total_tax,
                'total_sale'     => $total_sale,
                'uvt_value'      => $uvt,
                'customer_id'    => $request->customer_id,
                'seller_id'      => Auth::id(),
            ]);

            foreach ($items_to_save as $itemData) {
                SaleItem::create(array_merge($itemData, ['sale_id' => $sale->id_sale]));
                Product::where('id_product', $itemData['product_id'])->decrement('stock', $itemData['quantity']);
            }

            return response()->json(['status' => 'success', 'id_sale' => $sale->id_sale], 201);
        });
    }

    public function downloadInvoice($id)
    {
        $sale = Sale::with(['items.product', 'customer', 'seller.roles', 'payments.paymentMethod'])->findOrFail($id);

        $pdf = Pdf::loadView('sales.invoice', compact('sale'));
        return $pdf->stream("Factura_{$sale->invoice_number}.pdf");
    }

    // Generador de números de factura (POS-20260217-00001)
    private function nextInvoice(): string
    {
        $last = DB::table('sales')->max('id_sale') ?? 0;
        return 'POS-' . now()->format('Ymd') . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
