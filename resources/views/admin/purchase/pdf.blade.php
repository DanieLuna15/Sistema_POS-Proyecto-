<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Compra N° {{ str_pad($purchase->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 12mm 14mm 14mm 14mm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        table { width: 100%; border-collapse: collapse; border-spacing: 0; }
        td, th { padding: 0; vertical-align: top; }
        .header-table { margin-bottom: 14px; border-bottom: 2px solid #0f766e; padding-bottom: 12px; }
        .company-title { font-size: 20px; font-weight: 800; color: #0f172a; text-transform: uppercase; margin: 0 0 3px 0; }
        .company-subtitle { font-size: 11px; font-weight: 600; color: #0f766e; margin: 0 0 5px 0; text-transform: uppercase; }
        .company-info { font-size: 10px; color: #475569; line-height: 1.45; }
        .invoice-card { border: 1.5px solid #0f766e; border-radius: 6px; background-color: #f8fafc; text-align: center; overflow: hidden; }
        .invoice-card-header { background-color: #134e4a; color: #ffffff; font-weight: bold; font-size: 12px; padding: 5px 8px; text-transform: uppercase; }
        .invoice-card-body { padding: 8px 10px; }
        .invoice-number { font-size: 18px; font-weight: 800; color: #0f766e; margin: 2px 0 4px 0; }
        .info-card { border: 1px solid #cbd5e1; border-radius: 5px; background-color: #ffffff; padding: 8px 10px; margin-bottom: 14px; }
        .info-card-title { font-size: 10px; font-weight: bold; color: #0f172a; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 6px; }
        .info-label { font-weight: bold; color: #475569; font-size: 10px; width: 95px; }
        .info-value { color: #0f172a; font-size: 10.5px; }
        .items-table { width: 100%; margin-bottom: 12px; border: 1px solid #cbd5e1; border-radius: 5px; overflow: hidden; }
        .items-table th { background-color: #134e4a; color: #ffffff; font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 6px 8px; }
        .items-table td { padding: 6px 8px; font-size: 10.5px; border-bottom: 1px solid #e2e8f0; }
        .items-table tr:nth-child(even) td { background-color: #f8fafc; }
        .literal-box { background-color: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid #0f766e; border-radius: 4px; padding: 8px 10px; }
        .summary-card { border: 1px solid #cbd5e1; border-radius: 5px; overflow: hidden; }
        .summary-row td { padding: 5px 10px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
        .summary-total td { background-color: #134e4a; color: #ffffff; padding: 8px 10px; font-size: 13px; font-weight: 800; }
    </style>
</head>
<body>
    @php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp

    <table class="header-table">
        <tr>
            <td style="width: 62%;">
                <h1 class="company-title">{{ $activeCompany->name ?? 'SISTEMA POS & COMERCIAL' }}</h1>
                <div class="company-subtitle">{{ $activeCompany->slogan ?? 'Control de Abastecimiento y Compras de Inventario' }}</div>
                <div class="company-info">
                    <strong>NIT:</strong> {{ $activeCompany->nit ?? '1023847029' }} &bull; <strong>Sucursal Destino:</strong> {{ $purchase->branch->name ?? $activeBranch->name ?? 'Casa Matriz' }}<br>
                    <strong>Dirección:</strong> {{ $purchase->branch->address ?? $activeCompany->address ?? 'Av. Heroínas #742' }}<br>
                    <strong>Teléfono:</strong> {{ $purchase->branch->phone ?? $activeCompany->phone ?? '(+591) 4-4589210' }} &bull; <strong>Email:</strong> {{ $activeCompany->email ?? '' }}
                </div>
            </td>
            <td style="width: 38%; padding-left: 15px;">
                <div class="invoice-card">
                    <div class="invoice-card-header">NOTA DE COMPRA / INGRESO</div>
                    <div class="invoice-card-body">
                        <div class="invoice-number">N° {{ str_pad($purchase->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div style="font-size: 10px; color: #64748b;">
                            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y H:i') }}<br>
                            <strong>Estado:</strong> {{ $purchase->status }}
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-bottom: 14px;">
        <tr>
            <td style="width: 50%; padding-right: 6px;">
                <div class="info-card">
                    <div class="info-card-title">Datos del Proveedor</div>
                    <table>
                        <tr>
                            <td class="info-label">Proveedor:</td>
                            <td class="info-value"><strong>{{ $purchase->provider->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">NIT / RUC:</td>
                            <td class="info-value">{{ $purchase->provider->nit ?? 'S/N' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Teléfono:</td>
                            <td class="info-value">{{ $purchase->provider->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Email:</td>
                            <td class="info-value">{{ $purchase->provider->email ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 50%; padding-left: 6px;">
                <div class="info-card">
                    <div class="info-card-title">Datos de Recepción en Almacén</div>
                    <table>
                        <tr>
                            <td class="info-label">Recibido por:</td>
                            <td class="info-value">{{ $purchase->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Sucursal:</td>
                            <td class="info-value">{{ $purchase->branch->name ?? $activeBranch->name ?? 'Casa Matriz' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Moneda:</td>
                            <td class="info-value">{{ $activeCompany->currency_name ?? 'Bolivianos' }} ({{ $sym }})</td>
                        </tr>
                        <tr>
                            <td class="info-label">Impuesto Aplic.:</td>
                            <td class="info-value">{{ $purchase->tax }}%</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">#</th>
                <th style="width: 50%; text-align: left;">Producto Ingresado a Almacén</th>
                <th style="width: 14%; text-align: center;">Cantidad</th>
                <th style="width: 15%; text-align: right;">Costo Unit.</th>
                <th style="width: 15%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($PurchaseDetails as $index => $detail)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $detail->product->name }}</strong>
                    @if(!empty($detail->product->code))
                        <br><span style="font-size: 9px; color: #64748b;">Cód: {{ $detail->product->code }}</span>
                    @endif
                </td>
                <td style="text-align: center;">{{ $detail->quantity }} {{ $detail->product->unit_measure ?? 'UND' }}</td>
                <td style="text-align: right;">{{ $sym }} {{ number_format($detail->price, 2) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $sym }} {{ number_format($detail->quantity * $detail->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td style="width: 58%; padding-right: 12px;">
                <div class="literal-box">
                    <div style="font-size: 9px; font-weight: bold; color: #64748b; text-transform: uppercase;">Importe en Letras:</div>
                    <div style="font-size: 11px; font-weight: bold; font-style: italic;">Son: {{ $totalLiteral }}</div>
                </div>
            </td>
            <td style="width: 42%;">
                <div class="summary-card">
                    <table>
                        <tr class="summary-row">
                            <td style="text-align: right;"><strong>Subtotal Neto:</strong></td>
                            <td style="text-align: right; width: 110px;">{{ $sym }} {{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr class="summary-row">
                            <td style="text-align: right;"><strong>Impuesto ({{ $purchase->tax }}%):</strong></td>
                            <td style="text-align: right;">{{ $sym }} {{ number_format($subtotal * $purchase->tax / 100, 2) }}</td>
                        </tr>
                        <tr class="summary-total">
                            <td style="text-align: right;">TOTAL COMPRA:</td>
                            <td style="text-align: right;">{{ $sym }} {{ number_format($purchase->total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 38px; text-align: center;">
        <tr>
            <td style="width: 45%; border-top: 1px dashed #64748b; padding-top: 5px; font-size: 9.5px;">
                <strong>RECIBIDO CONFORME (ALMACÉN)</strong><br>{{ $purchase->user->name }}
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; border-top: 1px dashed #64748b; padding-top: 5px; font-size: 9.5px;">
                <strong>ENTREGADO POR PROVEEDOR</strong><br>{{ $purchase->provider->name }}
            </td>
        </tr>
    </table>
</body>
</html>
