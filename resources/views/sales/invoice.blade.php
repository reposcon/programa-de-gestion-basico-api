<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura_{{ $sale->invoice_number }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; }
        .brand-color { color: #007bff; }
        .header { border-bottom: 3px solid #007bff; padding-bottom: 10px; margin-bottom: 20px; }
        .clearfix { clear: both; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th { background: #007bff; color: white; padding: 8px; font-size: 10px; border: 1px solid #0069d9; }
        table td { padding: 8px; border-bottom: 1px solid #eee; }
        
        /* Sección de Totales y Pagos */
        .summary-container { margin-top: 20px; }
        .payments-side { float: left; width: 45%; }
        .totals-side { float: right; width: 45%; }
        .summary-table td { border: none; padding: 3px 0; }
        .grand-total { background: #007bff; color: white; font-weight: bold; font-size: 14px; }
        .grand-total td { padding: 8px !important; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div style="float: left;">
                <h1 class="brand-color" style="margin:0;">AdminPanel</h1>
                <p>NIT: 123.456.789-0</p>
            </div>
            <div style="float: right; text-align: right;">
                <h2 style="margin:0;">FACTURA DE VENTA</h2>
                <p><strong>Número:</strong> {{ $sale->invoice_number }}</p>
            </div>
            <div class="clearfix"></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>DESCRIPCIÓN</th>
                    <th style="text-align: center;">CANT.</th>
                    <th style="text-align: right;">UNITARIO</th>
                    <th style="text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->name_product }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->price_at_sale, 0) }}</td>
                        <td style="text-align: right;">${{ number_format($item->total_item, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-container">
            <div class="payments-side">
                <div style="border-bottom: 1px solid #007bff; font-weight: bold; margin-bottom: 5px; color: #007bff;">
                    DETALLE DE PAGOS
                </div>
                <table class="summary-table">
                    @foreach ($sale->payments as $payment)
                        <tr>
                            <td>{{ $payment->paymentMethod->name_payment_method }}:</td>
                            <td style="text-align: right;">${{ number_format($payment->amount_paid, 0) }}</td>
                        </tr>
                    @endforeach
                    <tr style="border-top: 1px dotted #ccc;">
                        <td style="font-weight: bold;">Total Recibido:</td>
                        <td style="text-align: right; font-weight: bold;">${{ number_format($sale->payments->sum('amount_paid'), 0) }}</td>
                    </tr>
                    @php $firstPayment = $sale->payments->first(); @endphp
                    @if($firstPayment && $firstPayment->change_returned > 0)
                        <tr>
                            <td style="color: #d9534f;">Cambio (Vueltas):</td>
                            <td style="text-align: right; color: #d9534f;">-${{ number_format($firstPayment->change_returned, 0) }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <div class="totals-side">
                <table class="summary-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td style="text-align: right;">${{ number_format($sale->subtotal, 0) }}</td>
                    </tr>
                    <tr>
                        <td>IVA Total:</td>
                        <td style="text-align: right;">${{ number_format($sale->total_tax, 0) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL NETO:</td>
                        <td style="text-align: right;">${{ number_format($sale->total_sale, 0) }}</td>
                    </tr>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #777;">
            <p>Atendido por: {{ $sale->seller->name_user }}</p>
            <p>Gracias por su compra en AdminPanel Cloud</p>
        </div>
    </div>
</body>
</html>