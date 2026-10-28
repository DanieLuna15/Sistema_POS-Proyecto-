<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comanda Cocina {{ $order->order_number }}</title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body { font-family: Arial, Helvetica, sans-serif; width: 76mm; margin: 0 auto; padding: 8px; font-size: 13px; color: #000; }
        .header-box { border: 2px solid #000; padding: 6px; text-align: center; margin-bottom: 8px; }
        .divider { border-top: 2px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { border-bottom: 2px solid #000; text-align: left; padding: 4px 0; font-size: 12px; }
        td { padding: 6px 0; vertical-align: top; border-bottom: 1px dashed #666; }
        .qty { font-size: 17px; font-weight: 900; width: 18%; }
        .dish { font-size: 15px; font-weight: bold; }
        .note { font-size: 12px; font-weight: bold; background: #eee; padding: 2px 4px; display: inline-block; margin-top: 2px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align:center; margin-bottom:10px;">
        <button onclick="window.print()" style="padding:8px 16px; font-weight:bold; cursor:pointer;">🖨️ Imprimir Comanda Cocina</button>
    </div>

    <div class="header-box">
        <div style="font-size:12px; font-weight:bold;">*** COMANDA DE COCINA / BARRA ***</div>
        <div style="font-size:20px; font-weight:900; margin:4px 0;">
            @if($order->service_type === 'MESA')
                {{ strtoupper($order->table->name ?? 'MESA') }}
            @else
                {{ $order->service_type }}: {{ strtoupper($order->customer_name) }}
            @endif
        </div>
        <div style="font-size:12px;">{{ $order->order_number }} &bull; Comensales: {{ $order->diners_count }}</div>
    </div>

    <div style="font-size:11px;">
        <strong>FECHA/HORA:</strong> {{ now('America/La_Paz')->format('d/m/Y H:i:s') }}<br>
        <strong>ATENDIDO POR:</strong> {{ $order->user->name ?? 'Mesero' }}
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>CANT</th>
                <th>PLATO / PREPARACIÓN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="qty">{{ number_format($item->quantity, 0) }}x</td>
                <td>
                    <div class="dish">{{ $item->product->name ?? 'Plato' }}</div>
                    @if($item->notes)
                        <div class="note">⚠️ NOTA: {{ strtoupper($item->notes) }}</div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($order->notes)
        <div class="divider"></div>
        <div style="font-weight:bold; font-size:12px;">OBSERVACIÓN GENERAL: {{ $order->notes }}</div>
    @endif
</body>
</html>

