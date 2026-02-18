<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura_{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #444;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        /* Estilo Azul Angular */
        .brand-color {
            color: #007bff;
        }

        .header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }

        .section-details {
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .clearfix {
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background: #007bff;
            color: white;
            text-transform: uppercase;
            font-size: 11px;
            padding: 10px;
            border: 1px solid #0069d9;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .total-section {
            float: right;
            width: 280px;
            margin-top: 20px;
        }

        .total-section table td {
            border: none;
            padding: 4px 10px;
        }

        .grand-total {
            background: #007bff;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div class="company-info">
                <h1 class="brand-color" style="margin:0;">AdminPanel</h1>
                <p style="margin:5px 0;">
                    <strong>NIT:</strong> 123.456.789-0<br>
                    Calle Falsa 123, Cali, Colombia
                </p>
            </div>
            <div class="invoice-info">
                <h2 style="margin:0; color: #333;">FACTURA DE VENTA</h2>
                <p style="margin:5px 0;">
                    <strong>Número:</strong> <span class="brand-color">{{ $sale->invoice_number }}</span><br>
                    <strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y h:i A') }}
                </p>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="section-details">
            <div style="float: left; width: 50%;">
                <strong class="brand-color">DATOS DEL CLIENTE</strong><br>
                Nombre: {{ $sale->customer->name_customer ?? ($sale->customer->name ?? 'Consumidor Final') }}<br>
                Documento: {{ $sale->customer->document_number ?? 'S/N' }}
            </div>
            <div style="float: right; width: 50%; text-align: right;">
                <strong class="brand-color">ATENDIDO POR</strong><br>
                {{ $sale->seller->name_user ?? 'Sistema' }}
                <small style="display:block; color:#777;">
                    @php
                        $rolName = $sale->seller->roles->first()->name_role ?? 'vendedor';
                    @endphp
                    @if ($rolName == 'admin') (Administrador)
                    @elseif($rolName == 'seller') (Asesor de Ventas)
                    @else ({{ $rolName }})
                    @endif
                </small>
            </div>
            <div class="clearfix"></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Descripción del Producto</th>
                    <th style="text-align: center;">Cant.</th>
                    <th style="text-align: right;">Precio Unit.</th>
                    <th style="text-align: center;">IVA</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->name_product }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->price_at_sale, 0) }}</td>
                        <td style="text-align: center;">{{ $item->tax_rate_at_sale }}%</td>
                        <td style="text-align: right;">${{ number_format($item->total_item, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right;">${{ number_format($sale->subtotal, 0) }}</td>
                </tr>
                <tr>
                    <td>IVA Total:</td>
                    <td style="text-align: right;">${{ number_format($sale->total_tax, 0) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>TOTAL:</td>
                    <td style="text-align: right;">${{ number_format($sale->total_sale, 0) }}</td>
                </tr>
            </table>
        </div>
        <div class="clearfix"></div>

        <div class="footer">
            <p>
                Valor UVT 2026: ${{ number_format($sale->uvt_value, 0) }}<br>
                Esta factura se asimila en todos sus efectos legales a una letra de cambio según el Art. 774 del código de comercio.<br>
                <strong>AdminPanel Cloud v1.0 - Software de Gestión</strong>
            </p>
        </div>
    </div>
</body>

</html>