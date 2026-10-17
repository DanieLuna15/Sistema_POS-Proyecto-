<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket 80mm - Venta #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            /* Fuente limpia, moderna y altamente legible (Sans-serif) */
            font-family: Arial, 'Segoe UI', Roboto, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            color: #111827;
            background-color: #0f172a;
            padding: 20px 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Barra de acciones en pantalla (Oculta al imprimir o en PDF) */
        .actions-toolbar {
            max-width: 440px;
            margin: 0 auto 16px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            align-items: center;
            background-color: #1e293b;
            padding: 10px 14px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .btn-action {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            text-decoration: none;
            cursor: pointer;
            border: none;
            text-align: center;
            transition: all 0.2s ease;
        }
        .btn-print {
            background-color: #fbbf24;
            color: #1e293b;
        }
        .btn-print:hover {
            background-color: #f59e0b;
        }
        .btn-pdf {
            background-color: #3b82f6;
            color: #ffffff;
        }
        .btn-pdf:hover {
            background-color: #2563eb;
        }
        .btn-a4 {
            background-color: #ef4444;
            color: #ffffff;
        }
        .btn-a4:hover {
            background-color: #dc2626;
        }
        .btn-pos {
            background-color: #10b981;
            color: #ffffff;
        }
        .btn-pos:hover {
            background-color: #059669;
        }
        .btn-back {
            background-color: #64748b;
            color: #ffffff;
        }
        .btn-back:hover {
            background-color: #475569;
        }

        /* Contenedor del Ticket Térmico de 80mm */
        .ticket-wrapper {
            width: 78mm;
            max-width: 320px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 14px 12px 20px 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border-radius: 4px;
        }

        /* Estilos Internos del Ticket */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .company-header {
            text-align: center;
            margin-bottom: 6px;
        }
        .company-name {
            font-size: 13.5px;
            font-weight: 800;
            color: #000000;
            letter-spacing: 0.2px;
        }
        .company-sub {
            font-size: 9.5px;
            color: #374151;
            margin-top: 1px;
            line-height: 1.3;
        }

        .divider {
            border-top: 1px dashed #6b7280;
            margin: 6px 0;
        }
        .divider-double {
            border-top: 2px solid #111827;
            margin: 6px 0;
        }

        /* Tabla de Productos */
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin: 4px 0;
        }
        .ticket-table th {
            border-top: 1px dashed #4b5563;
            border-bottom: 1px dashed #4b5563;
            padding: 4px 0;
            font-size: 10px;
            font-weight: bold;
            text-align: left;
        }
        .ticket-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .total-highlight {
            font-size: 13px;
            font-weight: bold;
            padding: 5px 0;
            border-top: 1.5px solid #000000;
            border-bottom: 1.5px solid #000000;
            margin: 5px 0;
        }

        .credit-info {
            background-color: #f8fafc;
            padding: 6px;
            border: 1px dashed #64748b;
            margin: 6px 0;
            font-size: 10px;
        }

        .qr-area {
            text-align: center;
            margin: 8px 0 4px 0;
        }
        .qr-area img {
            width: 105px;
            height: 105px;
            display: inline-block;
        }

        .footer-thanks {
            text-align: center;
            margin-top: 6px;
            font-size: 9.5px;
            color: #374151;
            line-height: 1.35;
        }

        /* Reglas de Impresión Nativa (Oculta botones y quita fondos oscuros) */
        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .actions-toolbar {
                display: none !important;
            }
            .ticket-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 2mm 3mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body>

    @if(!request()->has('pdf'))
    <!-- BARRA SUPERIOR DE ACCIONES (SOLO PANTALLA) -->
    <div class="actions-toolbar">
        <button onclick="window.print();" class="btn-action btn-print">
            &#128438; Imprimir Ticket (Ctrl+P)
        </button>
        <a href="{{ route('sales.ticket', ['sale' => $sale, 'pdf' => 1]) }}" target="_blank" class="btn-action btn-pdf">
            &#128196; PDF 80mm
        </a>
        <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn-action btn-a4">
            &#128196; Nota A4
        </a>
        @if($sale->sale_type === 'POS_RAPIDO')
        <a href="{{ route('sales.pos') }}" class="btn-action btn-pos">
            &#9889; Nueva Venta POS
        </a>
        @endif
        <a href="{{ route('sales.index') }}" class="btn-action btn-back">
            &#8592; Lista Ventas
        </a>
    </div>
    @endif

    <!-- TICKET DE 80MM -->
    <div class="ticket-wrapper">

        <!-- ENCABEZADO -->
        <div class="company-header">
            <div class="company-name">SISTEMA POS & COMERCIAL</div>
            <div class="company-sub">VENTA Y DISTRIBUCIÓN DE PRODUCTOS</div>
            <div class="company-sub">NIT: 1023847029 &bull; AUT: 4920018471</div>
            <div class="company-sub">Av. Heroínas #742 - Cochabamba</div>
            <div class="company-sub">Tel: (+591) 4-4589210 &bull; 71234567</div>
        </div>

        <div class="divider-double"></div>

        <!-- DATOS DEL COMPROBANTE -->
        <div class="text-center font-bold" style="font-size: 12.5px; margin: 2px 0;">
            TICKET NOTA DE VENTA
        </div>
        <div class="text-center font-bold" style="font-size: 13.5px; margin-bottom: 3px; color: #1e3a8a;">
            N°: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}
        </div>

        <div class="divider"></div>

        <table style="width: 100%; font-size: 10.5px; border-collapse: collapse;">
            <tr>
                <td style="width: 32%; font-weight: bold;">FECHA/HORA:</td>
                <td style="width: 68%;">{{ Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">MODALIDAD:</td>
                <td>
                    @if($sale->sale_type === 'POS_RAPIDO')
                        CAJA RÁPIDA (POS)
                    @elseif($sale->sale_type === 'CREDITO')
                        VENTA A CRÉDITO
                    @else
                        VENTA REGULAR
                    @endif
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">CAJERO/VEND:</td>
                <td>{{ $sale->user->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">CLIENTE:</td>
                <td><strong>{{ $sale->client->name }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">NIT/CI:</td>
                <td>{{ $sale->client->ci ?? $sale->client->nit ?? 'S/N' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">CONDICIÓN:</td>
                <td><strong>{{ $sale->payment_condition ?? ($sale->sale_type === 'CREDITO' ? 'CRÉDITO' : 'CONTADO') }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">FORMA PAGO:</td>
                <td>{{ $sale->payment_method ?? 'EFECTIVO' }}</td>
            </tr>
            @if($sale->payment_reference)
            <tr>
                <td style="font-weight: bold;">N° REF/VOUCHER:</td>
                <td>{{ $sale->payment_reference }}</td>
            </tr>
            @endif
        </table>

        <div class="divider"></div>

        <!-- DETALLE DE ARTÍCULOS -->
        <table class="ticket-table">
            <thead>
                <tr>
                    <th style="width: 15%;">CANT</th>
                    <th style="width: 47%;">DESCRIPCIÓN</th>
                    <th style="width: 18%; text-align: right;">P.U.</th>
                    <th style="width: 20%; text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php $items = $SaleDetails ?? $sale->saleDetails ?? []; @endphp
                @foreach ($items as $detail)
                @php
                    $itemTotal = ($detail->quantity * $detail->price) - (($detail->quantity * $detail->price * $detail->discount) / 100);
                @endphp
                <tr>
                    <td style="font-weight: bold;">{{ $detail->quantity }}</td>
                    <td>
                        {{ $detail->product->name }}
                        @if($detail->discount > 0)
                            <br><span style="font-size: 9px; color: #b91c1c;">(Desc. {{ $detail->discount }}%)</span>
                        @endif
                    </td>
                    <td style="text-align: right;">{{ number_format($detail->price, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($itemTotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- TOTALES -->
        <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
            <tr>
                <td style="text-align: right; padding-right: 8px;">SUBTOTAL:</td>
                <td style="text-align: right; width: 38%;">Bs. {{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($descuentototal > 0)
            <tr>
                <td style="text-align: right; padding-right: 8px; color: #b91c1c;">DESCUENTO TOTAL:</td>
                <td style="text-align: right; width: 38%; color: #b91c1c;">-Bs. {{ number_format($descuentototal, 2) }}</td>
            </tr>
            @endif
        </table>

        <div class="total-highlight">
            <table style="width: 100%; font-size: 12.5px;">
                <tr>
                    <td style="font-weight: bold;">TOTAL A PAGAR:</td>
                    <td style="text-align: right; font-weight: bold;">Bs. {{ number_format($sale->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- PAGO EN EFECTIVO Y CAMBIO -->
        @if($sale->cash_received)
        <table style="width: 100%; font-size: 10.5px; margin: 3px 0;">
            <tr>
                <td style="text-align: right; padding-right: 8px;">EFECTIVO RECIBIDO:</td>
                <td style="text-align: right; width: 38%; font-weight: bold;">Bs. {{ number_format($sale->cash_received, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 8px;">CAMBIO / VUELTO:</td>
                <td style="text-align: right; width: 38%; font-weight: bold; color: #047857;">Bs. {{ number_format($sale->cash_change, 2) }}</td>
            </tr>
        </table>
        @endif

        <!-- INFORMACIÓN DE VENTA A CRÉDITO -->
        @if($sale->sale_type === 'CREDITO')
        <div class="credit-info">
            <div class="font-bold text-center" style="margin-bottom: 3px; color: #1e3a8a;">*** PLAN DE PAGOS A CRÉDITO ***</div>
            <table style="width: 100%; font-size: 10px;">
                <tr>
                    <td>CUOTA INICIAL:</td>
                    <td style="text-align: right; font-weight: bold;">Bs. {{ number_format($sale->down_payment, 2) }}</td>
                </tr>
                <tr>
                    <td>SALDO PENDIENTE:</td>
                    <td style="text-align: right; font-weight: bold; color: #b91c1c;">Bs. {{ number_format($sale->balance_pending, 2) }}</td>
                </tr>
                <tr>
                    <td>N° DE CUOTAS:</td>
                    <td style="text-align: right;">{{ $sale->installments_count }} ({{ $sale->installment_frequency ?? 'MENSUAL' }})</td>
                </tr>
                <tr>
                    <td>ESTADO CRÉDITO:</td>
                    <td style="text-align: right; font-weight: bold;">{{ $sale->credit_status ?? 'PENDIENTE' }}</td>
                </tr>
            </table>

            @if(isset($installments) && $installments->count() > 0)
            <div class="divider" style="margin: 4px 0;"></div>
            <table style="width: 100%; font-size: 9px; text-align: center;">
                <tr style="font-weight: bold;">
                    <td>N°</td>
                    <td>VENCE</td>
                    <td>MONTO</td>
                    <td>ESTADO</td>
                </tr>
                @foreach($installments as $inst)
                <tr>
                    <td>#{{ $inst->installment_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d/m/Y') }}</td>
                    <td>Bs. {{ number_format($inst->amount, 2) }}</td>
                    <td>{{ $inst->status }}</td>
                </tr>
                @endforeach
            </table>
            @endif
        </div>
        @endif

        <div style="font-size: 9px; margin: 5px 0; text-align: center; font-style: italic; color: #374151;">
            SON: {{ $totalLiteral }}
        </div>

        <div class="divider"></div>

        <!-- CÓDIGO QR DE VERIFICACIÓN -->
        @php
            $qrContent = "SISTEMA POS | NOTA DE VENTA N° " . str_pad($sale->id, 6, '0', STR_PAD_LEFT) . 
                         "\nNIT: 1023847029" . 
                         "\nFECHA: " . \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') . 
                         "\nCLIENTE: " . ($sale->client->name ?? 'S/N') . 
                         "\nTOTAL: Bs. " . number_format($sale->total, 2);
            $qrSvgBase64 = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(0)->errorCorrection('M')->generate($qrContent));
        @endphp

        <div class="qr-area">
            <img src="data:image/svg+xml;base64,{{ $qrSvgBase64 }}" alt="Código QR de la Venta">
            <div style="font-size: 10px; font-weight: bold; margin-top: 3px; color: #111827;">
                *VTA-{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}*
            </div>
            <div style="font-size: 8.5px; color: #6b7280;">
                Escanee el código QR para validar este comprobante
            </div>
        </div>

        <div class="footer-thanks">
            <strong>¡GRACIAS POR SU COMPRA!</strong><br>
            Conserve este ticket para cualquier reclamo o garantía.<br>
            <em>Sistema POS &bull; Control Interno</em>
        </div>

        <div class="divider-double"></div>

    </div>

    @if(!request()->has('pdf'))
    <script>
        // Si no se pasó noprint en la URL, disparar la impresión directa tras medio segundo
        window.addEventListener('DOMContentLoaded', function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('noprint')) {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });
    </script>
    @endif

</body>
</html>
