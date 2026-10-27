<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Arqueo de Caja N° {{ str_pad($cashRegister->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 14mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; color: #0f172a; }
        .box { border: 1px solid #cbd5e1; border-radius: 5px; padding: 10px; margin-bottom: 14px; }
        .summary-table th, .summary-table td { border: 1px solid #cbd5e1; padding: 7px 10px; font-size: 11px; }
        .summary-table th { background: #1e293b; color: #fff; text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    @php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
    <table class="header">
        <tr>
            <td style="width: 65%;">
                <div class="title">{{ $activeCompany->name ?? 'SISTEMA POS & COMERCIAL' }}</div>
                <div><strong>Sucursal:</strong> {{ $cashRegister->branch->name ?? $activeBranch->name ?? 'Casa Matriz' }}</div>
                <div><strong>NIT:</strong> {{ $activeCompany->nit ?? 'S/N' }} &bull; <strong>Tel:</strong> {{ $activeCompany->phone ?? '' }}</div>
            </td>
            <td style="width: 35%; text-align: right;">
                <div style="font-size: 15px; font-weight: bold; color: #2563eb;">ARQUEO DE CAJA #{{ str_pad($cashRegister->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Estado:</strong> {{ $cashRegister->status }}</div>
                <div><strong>Cajero:</strong> {{ $cashRegister->user->name }}</div>
            </td>
        </tr>
    </table>

    <div class="box">
        <table>
            <tr>
                <td><strong>Apertura:</strong> {{ \Carbon\Carbon::parse($cashRegister->opening_date)->format('d/m/Y H:i:s') }}</td>
                <td><strong>Cierre:</strong> {{ $cashRegister->closing_date ? \Carbon\Carbon::parse($cashRegister->closing_date)->format('d/m/Y H:i:s') : 'En curso' }}</td>
            </tr>
        </table>
    </div>

    <table class="summary-table" style="margin-bottom: 18px;">
        <thead>
            <tr>
                <th>Concepto de Arqueo</th>
                <th class="text-right">Importe ({{ $sym }})</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1. Monto Inicial de Apertura en Gaveta</td>
                <td class="text-right">{{ $sym }} {{ number_format($cashRegister->initial_amount, 2) }}</td>
            </tr>
            <tr>
                <td>2. (+) Ventas Cobradas en Efectivo</td>
                <td class="text-right">+ {{ $sym }} {{ number_format($cashRegister->sales_cash, 2) }}</td>
            </tr>
            <tr>
                <td>3. (+) Cobros de Cuotas de Créditos</td>
                <td class="text-right">+ {{ $sym }} {{ number_format($cashRegister->credit_collections, 2) }}</td>
            </tr>
            <tr>
                <td>4. (+) Otros Ingresos de Caja Chica</td>
                <td class="text-right">+ {{ $sym }} {{ number_format($cashRegister->incomes_amount, 2) }}</td>
            </tr>
            <tr>
                <td>5. (-) Gastos Operativos y Egresos de Caja Chica</td>
                <td class="text-right">- {{ $sym }} {{ number_format($cashRegister->expenses_amount, 2) }}</td>
            </tr>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td>SALDO ESPERADO EN EFECTIVO EN CAJA</td>
                <td class="text-right">{{ $sym }} {{ number_format($cashRegister->expected_amount, 2) }}</td>
            </tr>
            @if($cashRegister->counted_amount !== null)
            <tr style="font-weight: bold;">
                <td>EFECTIVO FÍSICO CONTADO AL CIERRE</td>
                <td class="text-right">{{ $sym }} {{ number_format($cashRegister->counted_amount, 2) }}</td>
            </tr>
            <tr style="font-weight: bold;">
                <td>DIFERENCIA DE ARQUEO</td>
                <td class="text-right">{{ $sym }} {{ number_format($cashRegister->difference, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td>Ventas en Medios Digitales (QR / Transferencia / Tarjeta)</td>
                <td class="text-right">{{ $sym }} {{ number_format($cashRegister->sales_other, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($cashRegister->movements->count() > 0)
    <h4>Detalle de Movimientos de Caja Chica</h4>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Fecha/Hora</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashRegister->movements as $m)
            <tr>
                <td>{{ \Carbon\Carbon::parse($m->movement_date)->format('d/m/Y H:i') }}</td>
                <td>{{ $m->type }}</td>
                <td>{{ $m->category }}</td>
                <td>{{ $m->description }}</td>
                <td class="text-right">{{ $sym }} {{ number_format($m->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table style="margin-top: 45px; text-align: center;">
        <tr>
            <td style="width: 45%; border-top: 1px dashed #475569; padding-top: 5px;">
                Firma Cajero Responsable<br>{{ $cashRegister->user->name }}
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; border-top: 1px dashed #475569; padding-top: 5px;">
                Firma Supervisor / Administración
            </td>
        </tr>
    </table>
</body>
</html>

