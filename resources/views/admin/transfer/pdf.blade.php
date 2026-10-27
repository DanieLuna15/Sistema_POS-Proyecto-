<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guía de Traspaso {{ $transfer->reference_code }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 20px; }
        .header { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 15px; }
        .title-box { background: #1e3a8a; color: #fff; padding: 8px 12px; border-radius: 4px; text-align: center; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 6px 8px; border: 1px solid #cbd5e1; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th { background: #f1f5f9; border: 1px solid #94a3b8; padding: 8px; text-align: left; font-size: 11px; }
        .items-table td { border: 1px solid #cbd5e1; padding: 7px 8px; }
        .signatures { width: 100%; margin-top: 65px; text-align: center; }
        .sig-line { border-top: 1px solid #334155; width: 80%; margin: 0 auto; padding-top: 5px; font-size: 11px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 65%;">
                <h2 style="margin:0; color:#1e3a8a;">{{ $company->name }}</h2>
                <div style="font-size:11px; color:#475569;">
                    NIT: {{ $company->nit }} &bull; Tel: {{ $company->phone }}<br>
                    {{ $company->address }} ({{ $company->city }})
                </div>
            </td>
            <td style="width: 35%;">
                <div class="title-box">
                    <div style="font-size:11px; font-weight:bold;">GUÍA DE REMISIÓN / TRASPASO</div>
                    <div style="font-size:16px; font-weight:bold; margin-top:2px;">{{ $transfer->reference_code }}</div>
                    <div style="font-size:10px; margin-top:2px;">ESTADO: {{ $transfer->status }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width:50%; background:#fef2f2;">
                <strong style="color:#991b1b;">SUCURSAL DE ORIGEN (DESPACHA):</strong><br>
                <strong>{{ $transfer->fromBranch->name ?? 'Origen' }} ({{ $transfer->fromBranch->code ?? '' }})</strong><br>
                <small>{{ $transfer->fromBranch->address ?? '' }} - {{ $transfer->fromBranch->city ?? '' }}</small>
            </td>
            <td style="width:50%; background:#f0fdf4;">
                <strong style="color:#166534;">SUCURSAL DE DESTINO (RECIBE):</strong><br>
                <strong>{{ $transfer->toBranch->name ?? 'Destino' }} ({{ $transfer->toBranch->code ?? '' }})</strong><br>
                <small>{{ $transfer->toBranch->address ?? '' }} - {{ $transfer->toBranch->city ?? '' }}</small>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Fecha y Hora de Emisión:</strong> {{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d/m/Y H:i:s') }}
            </td>
            <td>
                <strong>Autorizado / Registrado por:</strong> {{ $transfer->user->name ?? 'Administrador' }}
            </td>
        </tr>
        @if($transfer->notes)
        <tr>
            <td colspan="2">
                <strong>Observaciones / Motivo de Traslado:</strong> {{ $transfer->notes }}
            </td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:8%;">#</th>
                <th style="width:18%;">CÓDIGO / SKU</th>
                <th style="width:46%;">DESCRIPCIÓN DEL PRODUCTO</th>
                <th style="width:14%; text-align:center;">UNIDAD</th>
                <th style="width:14%; text-align:center;">CANTIDAD</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfer->details as $idx => $det)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $det->product->code ?? $det->product_id }}</strong>
                    @if($det->product && $det->product->sku)
                        <br><small>{{ $det->product->sku }}</small>
                    @endif
                </td>
                <td>{{ $det->product->name ?? 'Producto' }}</td>
                <td style="text-align:center;">{{ $det->product->unit_measure ?? 'UND' }}</td>
                <td style="text-align:center; font-weight:bold; font-size:13px;">{{ number_format($det->quantity, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td style="width:33%;">
                <div class="sig-line">
                    <strong>ENTREGADO POR (ORIGEN)</strong><br>
                    {{ $transfer->fromBranch->name ?? '' }}
                </div>
            </td>
            <td style="width:33%;">
                <div class="sig-line">
                    <strong>ENCARGADO DE TRANSPORTE</strong><br>
                    Firma y C.I.
                </div>
            </td>
            <td style="width:33%;">
                <div class="sig-line">
                    <strong>RECIBIDO CONFORME (DESTINO)</strong><br>
                    {{ $transfer->toBranch->name ?? '' }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>

