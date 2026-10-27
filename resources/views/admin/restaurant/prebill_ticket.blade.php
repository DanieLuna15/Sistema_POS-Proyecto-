<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pre-Cuenta {{ $order->order_number }}</title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body { font-family: Arial, Helvetica, sans-serif; width: 76mm; margin: 0 auto; padding: 8px; font-size: 11.5px; color: #000; }
        .center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { border-bottom: 1px solid #000; text-align: left; padding: 4px 0; font-size: 10.5px; }
        td { padding: 4px 0; }
        .box-factura { border: 1px solid #000; padding: 6px; margin-top: 10px; font-size: 10.5px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body onload="window.print()">
    @php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
    <div class="no-print" style="text-align:center; margin-bottom:10px;">
        <button onclick="window.print()" style="padding:8px 16px; font-weight:bold; cursor:pointer;">🖨️ Imprimir Pre-Cuenta</button>
    </div>

    <div class="center">
        <strong style="font-size:14px;">{{ $activeCompany->name }}</strong><br>
        <span style="font-size:11px;">ESTADO DE CUENTA / PRE-CUENTA</span><br>
        <strong style="font-size:16px;">
            {{ $order->service_type === 'MESA' ? ($order->table->name ?? 'Mesa') : ($order->service_type . ': ' . $order->customer_name) }}
        </strong>
    </div>

    <div class="divider"></div>
    <div>
        <strong>Comanda:</strong> {{ $order->order_number }} &bull; <strong>Hora:</strong> {{ now('America/La_Paz')->format('d/m/Y H:i') }}<br>
        <strong>Atendido por:</strong> {{ $order->user->name ?? 'Mesero' }}
    </div>
    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="width:15%;">CANT</th>
                <th style="width:55%;">DETALLE</th>
                <th style="width:30%; text-align:right;">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ number_format($item->quantity, 0) }}</td>
                <td>{{ $item->product->name ?? 'Plato' }}</td>
                <td style="text-align:right;">{{ number_format($item->quantity * $item->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>
    <div style="display:flex; justify-content:space-between; font-size:15px; font-weight:bold;">
        <span>TOTAL CONSUMO:</span>
        <span>{{ $sym }} {{ number_format($order->total, 2) }}</span>
    </div>

    <div class="box-factura">
        <strong>DATOS PARA SU FACTURA / RECIBO:</strong><br><br>
        NIT / C.I.: _______________________________<br><br>
        NOMBRE / RAZÓN SOCIAL:<br><br>
        __________________________________________<br><br>
        PROPINA VOLUNTARIA: ______________________
    </div>

    <div class="center" style="margin-top:8px; font-size:10px;">
        <em>* Este documento es una pre-cuenta informativa, exija su comprobante oficial en caja. *</em>
    </div>
</body>
</html>
