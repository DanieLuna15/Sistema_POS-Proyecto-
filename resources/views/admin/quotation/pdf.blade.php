<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proforma / Cotización N° {{ str_pad($quotation->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 12mm 14mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        .header-table { border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 14px; }
        .company-title { font-size: 20px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin: 0 0 3px 0; }
        .company-subtitle { font-size: 11px; font-weight: bold; color: #2563eb; margin-bottom: 5px; }
        .company-info { font-size: 10px; color: #475569; line-height: 1.45; }
        .invoice-card { border: 1.5px solid #2563eb; border-radius: 6px; background: #f8fafc; text-align: center; overflow: hidden; }
        .invoice-card-header { background: #1e293b; color: #fff; font-weight: bold; font-size: 12px; padding: 6px; }
        .invoice-number { font-size: 17px; font-weight: bold; color: #2563eb; margin: 4px 0; }
        .info-card { border: 1px solid #cbd5e1; border-radius: 5px; padding: 8px 10px; margin-bottom: 14px; }
        .items-table { width: 100%; border: 1px solid #cbd5e1; margin-bottom: 14px; }
        .items-table th { background: #1e293b; color: #fff; font-size: 10px; padding: 6px 8px; text-transform: uppercase; }
        .items-table td { padding: 6px 8px; font-size: 10.5px; border-bottom: 1px solid #e2e8f0; }
        .literal-box { background: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid #2563eb; padding: 8px 10px; }
    </style>
</head>
<body>
    @php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
    <table class="header-table">
        <tr>
            <td style="width: 62%;">
                <h1 class="company-title">{{ $activeCompany->name ?? 'SISTEMA POS & COMERCIAL' }}</h1>
                <div class="company-subtitle">{{ $activeCompany->slogan ?? 'Venta y Distribución de Productos y Servicios' }}</div>
                <div class="company-info">
                    <strong>NIT:</strong> {{ $activeCompany->nit ?? '1023847029' }} &bull; <strong>Sucursal:</strong> {{ $quotation->branch->name ?? $activeBranch->name ?? 'Casa Matriz' }}<br>
                    <strong>Dirección:</strong> {{ $quotation->branch->address ?? $activeCompany->address }}<br>
                    <strong>Teléfono:</strong> {{ $quotation->branch->phone ?? $activeCompany->phone }} &bull; <strong>Email:</strong> {{ $activeCompany->email }}<br>
                    <strong>{{ $activeCompany->city ?? 'Cochabamba' }} - {{ $activeCompany->country ?? 'Bolivia' }}</strong>
                </div>
            </td>
            <td style="width: 38%; padding-left: 15px;">
                <div class="invoice-card">
                    <div class="invoice-card-header">COTIZACIÓN / PROFORMA</div>
                    <div style="padding: 8px;">
                        <div class="invoice-number">N° {{ str_pad($quotation->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div style="font-size: 10px; color: #64748b;">
                            <strong>Emisión:</strong> {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d/m/Y') }}<br>
                            <strong>Válida hasta:</strong> {{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') : '15 días' }}
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="info-card">
        <table>
            <tr>
                <td style="width: 55%;">
                    <strong>Cliente / Empresa:</strong> {{ $quotation->client->name }}<br>
                    <strong>NIT / CI:</strong> {{ $quotation->client->ci ?? $quotation->client->nit ?? 'S/N' }}<br>
                    <strong>Teléfono:</strong> {{ $quotation->client->phone ?? 'S/N' }}
                </td>
                <td style="width: 45%;">
                    <strong>Asesor Comercial:</strong> {{ $quotation->user->name }}<br>
                    <strong>Moneda:</strong> {{ $activeCompany->currency_name ?? 'Bolivianos' }} ({{ $sym }})<br>
                    <strong>Estado:</strong> {{ $quotation->status }}
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">#</th>
                <th style="width: 48%; text-align: left;">Descripción del Producto / Servicio</th>
                <th style="width: 12%; text-align: center;">Cantidad</th>
                <th style="width: 12%; text-align: right;">P. Unit.</th>
                <th style="width: 10%; text-align: center;">Desc.</th>
                <th style="width: 12%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->details as $idx => $det)
            @php
                $sub = ($det->quantity * $det->price) - (($det->quantity * $det->price * $det->discount) / 100);
            @endphp
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $det->product->name ?? '' }}</strong>
                    @if(!empty($det->product->code))
                        <br><span style="font-size: 9px; color: #64748b;">Cód: {{ $det->product->code }}</span>
                    @endif
                </td>
                <td style="text-align: center;">{{ $det->quantity }} {{ $det->product->unit_measure ?? 'UND' }}</td>
                <td style="text-align: right;">{{ $sym }} {{ number_format($det->price, 2) }}</td>
                <td style="text-align: center;">{{ $det->discount }}%</td>
                <td style="text-align: right; font-weight: bold;">{{ $sym }} {{ number_format($sub, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td style="width: 60%; padding-right: 12px;">
                <div class="literal-box">
                    <strong>Importe en Letras:</strong><br>
                    <em>Son: {{ $totalLiteral }}</em>
                </div>
                @if($quotation->notes)
                    <p style="margin-top: 6px;"><strong>Observaciones:</strong> {{ $quotation->notes }}</p>
                @endif
                @if($quotation->terms)
                    <p style="margin-top: 4px; color: #475569; font-size: 9.5px;"><strong>Condiciones:</strong> {{ $quotation->terms }}</p>
                @endif
            </td>
            <td style="width: 40%;">
                <table style="border: 1px solid #cbd5e1;">
                    <tr>
                        <td style="padding: 6px 10px; text-align: right;"><strong>Subtotal:</strong></td>
                        <td style="padding: 6px 10px; text-align: right;">{{ $sym }} {{ number_format($quotation->subtotal, 2) }}</td>
                    </tr>
                    @if($quotation->discount_total > 0)
                    <tr>
                        <td style="padding: 6px 10px; text-align: right; color: #b91c1c;"><strong>Descuento:</strong></td>
                        <td style="padding: 6px 10px; text-align: right; color: #b91c1c;">- {{ $sym }} {{ number_format($quotation->discount_total, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="background: #1e293b; color: #fff; font-size: 13px; font-weight: bold;">
                        <td style="padding: 8px 10px; text-align: right;">TOTAL COTIZADO:</td>
                        <td style="padding: 8px 10px; text-align: right;">{{ $sym }} {{ number_format($quotation->total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

