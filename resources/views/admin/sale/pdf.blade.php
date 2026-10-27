<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Venta N° {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 12mm 14mm 14mm 14mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Utilidades de Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        td, th {
            padding: 0;
            vertical-align: top;
        }

        /* Encabezado */
        .header-table {
            margin-bottom: 14px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
        }
        .company-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 3px 0;
        }
        .company-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: #2563eb;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .company-info {
            font-size: 10px;
            color: #475569;
            line-height: 1.45;
        }

        /* Badge / Recuadro de Venta */
        .invoice-card {
            border: 1.5px solid #2563eb;
            border-radius: 6px;
            background-color: #f8fafc;
            text-align: center;
            overflow: hidden;
        }
        .invoice-card-header {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            font-size: 12px;
            padding: 5px 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-card-body {
            padding: 8px 10px;
        }
        .invoice-number {
            font-size: 18px;
            font-weight: 800;
            color: #2563eb;
            margin: 2px 0 4px 0;
        }
        .invoice-date {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 5px;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-confirmed {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-cancelled {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
        .badge-type {
            display: inline-block;
            margin-top: 4px;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            background-color: #e0e7ff;
            color: #3730a3;
        }

        /* Bloques de Información (Cliente & Operación) */
        .info-section {
            margin-bottom: 14px;
        }
        .info-card {
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            background-color: #ffffff;
            padding: 8px 10px;
        }
        .info-card-title {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            font-size: 10px;
            width: 85px;
        }
        .info-value {
            color: #0f172a;
            font-size: 10.5px;
        }

        /* Tabla de Productos */
        .items-table {
            width: 100%;
            margin-bottom: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            overflow: hidden;
        }
        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 8px;
            letter-spacing: 0.3px;
        }
        .items-table td {
            padding: 6px 8px;
            font-size: 10.5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }

        /* Totales y Liquidación */
        .totals-table {
            margin-bottom: 14px;
        }
        .literal-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 8px;
        }
        .literal-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .literal-text {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            font-style: italic;
        }
        .summary-card {
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            background-color: #ffffff;
            overflow: hidden;
        }
        .summary-row {
            padding: 5px 10px;
            font-size: 11px;
            border-bottom: 1px solid #f1f5f9;
        }
        .summary-label {
            font-weight: 600;
            color: #475569;
            text-align: right;
            padding-right: 12px;
        }
        .summary-value {
            font-weight: bold;
            color: #0f172a;
            text-align: right;
            width: 105px;
        }
        .summary-total {
            background-color: #1e293b;
            color: #ffffff;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 800;
        }
        .summary-total .summary-label {
            color: #ffffff;
        }
        .summary-total .summary-value {
            color: #60a5fa;
            font-size: 14px;
        }

        /* Sección de Crédito (si aplica) */
        .credit-box {
            border: 1.5px solid #3b82f6;
            border-radius: 6px;
            background-color: #f0f7ff;
            padding: 8px 10px;
            margin-bottom: 14px;
        }
        .credit-title {
            font-size: 11px;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 6px;
            border-bottom: 1px solid #bfdbfe;
            padding-bottom: 3px;
        }
        .installments-table {
            width: 100%;
            background-color: #ffffff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            margin-top: 6px;
        }
        .installments-table th {
            background-color: #2563eb;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 4px 6px;
            text-transform: uppercase;
        }
        .installments-table td {
            font-size: 9.5px;
            padding: 4px 6px;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
        }

        /* Firmas y Pie de Documento */
        .signature-table {
            margin-top: 25px;
            margin-bottom: 12px;
        }
        .signature-line {
            width: 75%;
            border-top: 1px dashed #64748b;
            margin: 0 auto 4px auto;
        }
        .signature-text {
            font-size: 9.5px;
            color: #475569;
            text-align: center;
            font-weight: 600;
        }
        .footer-note {
            text-align: center;
            font-size: 9px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            margin-top: 10px;
            line-height: 1.4;
        }
        .qr-box {
            text-align: center;
            margin: 6px 0 4px 0;
        }
        .qr-box img {
            width: 85px;
            height: 85px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <!-- 1. ENCABEZADO Y DATOS DE LA EMPRESA -->
    <table class="header-table">
        <tr>
            <td style="width: 62%;">
                <h1 class="company-title">{{ $activeCompany->name ?? 'SISTEMA POS & COMERCIAL' }}</h1>
                <div class="company-subtitle">{{ $activeCompany->slogan ?? 'Venta y Distribución de Productos de Calidad' }}</div>
                <div class="company-info">
                    <strong>NIT:</strong> {{ $activeCompany->nit ?? '1023847029' }} &bull; <strong>Sucursal:</strong> {{ $sale->branch->name ?? $activeBranch->name ?? 'Casa Matriz' }}<br>
                    <strong>Dirección:</strong> {{ $sale->branch->address ?? $activeCompany->address ?? 'Av. Heroínas #742 esq. Ayacucho' }}<br>
                    <strong>Teléfono:</strong> {{ $sale->branch->phone ?? $activeCompany->phone ?? '(+591) 4-4589210' }} &bull; <strong>Email:</strong> {{ $activeCompany->email ?? 'ventas@possystem.bo' }}<br>
                    <strong>{{ $sale->branch->city ?? $activeCompany->city ?? 'Cochabamba' }} - {{ $activeCompany->country ?? 'Bolivia' }}</strong>
                </div>
            </td>
            <td style="width: 38%; padding-left: 15px;">
                <div class="invoice-card">
                    <div class="invoice-card-header">
                        NOTA DE VENTA
                    </div>
                    <div class="invoice-card-body">
                        <div class="invoice-number">N° {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="invoice-date">
                            <strong>Fecha:</strong> {{ Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }} &bull; 
                            <strong>Hora:</strong> {{ Carbon\Carbon::parse($sale->sale_date)->format('H:i') }}
                        </div>
                        <div>
                            @if($sale->status == 'CONFIRMADO')
                                <span class="badge-status badge-confirmed">Venta Confirmada</span>
                            @else
                                <span class="badge-status badge-cancelled">{{ $sale->status }}</span>
                            @endif
                        </div>
                        <div>
                            @if($sale->sale_type === 'POS_RAPIDO')
                                <span class="badge-type">MODALIDAD: CAJA RÁPIDA (POS)</span>
                            @elseif($sale->sale_type === 'CREDITO')
                                <span class="badge-type" style="background-color: #fef3c7; color: #92400e;">MODALIDAD: VENTA A CRÉDITO</span>
                            @else
                                <span class="badge-type">MODALIDAD: VENTA REGULAR</span>
                            @endif
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. DATOS DEL CLIENTE Y OPERACIÓN -->
    <table class="info-section">
        <tr>
            <td style="width: 50%; padding-right: 6px;">
                <div class="info-card">
                    <div class="info-card-title">Datos del Cliente</div>
                    <table>
                        <tr>
                            <td class="info-label">Señor(es):</td>
                            <td class="info-value"><strong>{{ $sale->client->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">NIT / CI:</td>
                            <td class="info-value">{{ $sale->client->ci ?? $sale->client->nit ?? 'S/N' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Teléfono:</td>
                            <td class="info-value">{{ $sale->client->phone ?? 'Sin registrar' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Dirección:</td>
                            <td class="info-value">{{ $sale->client->address ?? 'Cochabamba, Bolivia' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 50%; padding-left: 6px;">
                <div class="info-card">
                    <div class="info-card-title">Detalles de Operación</div>
                    <table>
                        <tr>
                            <td class="info-label">Atendido por:</td>
                            <td class="info-value">{{ $sale->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Condición:</td>
                            <td class="info-value"><strong>{{ $sale->payment_condition ?? ($sale->sale_type === 'CREDITO' ? 'CRÉDITO' : 'CONTADO') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Forma de Pago:</td>
                            <td class="info-value"><strong>{{ $sale->payment_method ?? 'EFECTIVO' }}</strong></td>
                        </tr>
                        @if($sale->payment_reference)
                        <tr>
                            <td class="info-label">N° Referencia:</td>
                            <td class="info-value">{{ $sale->payment_reference }}</td>
                        </tr>
                        @endif
                        @if($sale->cash_received)
                        <tr>
                            <td class="info-label">Efectivo Rec.:</td>
                            <td class="info-value">Bs. {{ number_format($sale->cash_received, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Cambio/Vuelto:</td>
                            <td class="info-value">Bs. {{ number_format($sale->cash_change, 2) }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="info-label">Moneda:</td>
                            <td class="info-value">Bolivianos (Bs.)</td>
                        </tr>
                        <tr>
                            <td class="info-label">Estado Pago:</td>
                            <td class="info-value">
                                @if($sale->sale_type === 'CREDITO')
                                    <span style="color: #d97706; font-weight: bold;">{{ $sale->credit_status ?? 'PENDIENTE' }}</span>
                                @else
                                    <span style="color: #15803d; font-weight: bold;">PAGADO TOTAL</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- 3. TABLA DE PRODUCTOS -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 47%; text-align: left;">Descripción del Artículo</th>
                <th style="width: 12%; text-align: center;">Cantidad</th>
                <th style="width: 12%; text-align: right;">Precio Unit.</th>
                <th style="width: 10%; text-align: center;">Desc. (%)</th>
                <th style="width: 14%; text-align: right;">Subtotal (Bs.)</th>
            </tr>
        <tbody>
            @php $items = $SaleDetails ?? $sale->saleDetails ?? []; @endphp
            @foreach ($items as $index => $detail)
            @php
                $itemSubtotal = ($detail->quantity * $detail->price) - (($detail->quantity * $detail->price * $detail->discount) / 100);
            @endphp
            <tr>
                <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $detail->product->name }}</strong>
                    @if(isset($detail->product->code))
                        <br><span style="font-size: 8.5px; color: #64748b;">Cód: {{ $detail->product->code }}</span>
                    @endif
                </td>
                <td style="text-align: center;">{{ $detail->quantity }} unid.</td>
                <td style="text-align: right;">Bs. {{ number_format($detail->price, 2) }}</td>
                <td style="text-align: center;">
                    @if($detail->discount > 0)
                        <span style="color: #b91c1c; font-weight: bold;">{{ $detail->discount }}%</span>
                    @else
                        <span style="color: #94a3b8;">0%</span>
                    @endif
                </td>
                <td style="text-align: right; font-weight: bold; color: #0f172a;">
                    Bs. {{ number_format($itemSubtotal, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 4. TOTALES Y MONTO EN LETRAS -->
    <table class="totals-table">
        <tr>
            <td style="width: 58%; padding-right: 12px;">
                <div class="literal-box">
                    <div class="literal-title">Importe en Letras:</div>
                    <div class="literal-text">Son: {{ $totalLiteral }}</div>
                </div>
                <div style="font-size: 9.5px; color: #64748b; line-height: 1.4; padding: 4px;">
                    &bull; Este comprobante acredita la entrega de la mercadería detallada.<br>
                    &bull; Todo reclamo o cambio debe efectuarse dentro de las 48 horas presentando esta nota.
                </div>
            </td>
            <td style="width: 42%;">
                <div class="summary-card">
                    <table style="width: 100%;">
                        <tr class="summary-row">
                            <td class="summary-label">Subtotal:</td>
                            <td class="summary-value">Bs. {{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @if($descuentototal > 0)
                        <tr class="summary-row">
                            <td class="summary-label" style="color: #b91c1c;">Descuento Total:</td>
                            <td class="summary-value" style="color: #b91c1c;">- Bs. {{ number_format($descuentototal, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="summary-total">
                            <td class="summary-label">TOTAL A PAGAR:</td>
                            <td class="summary-value">Bs. {{ number_format($sale->total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- 5. SI ES VENTA A CRÉDITO: SECCIÓN DE FINANCIAMIENTO Y CUOTAS -->
    @if($sale->sale_type === 'CREDITO')
    <div class="credit-box">
        <div class="credit-title">
            Plan de Pagos y Financiamiento a Crédito
        </div>
        <table style="width: 100%; margin-bottom: 6px;">
            <tr>
                <td style="width: 25%; font-size: 10px;">
                    <strong>Cuota Inicial:</strong><br>
                    <span style="font-size: 12px; font-weight: bold; color: #166534;">Bs. {{ number_format($sale->down_payment, 2) }}</span>
                </td>
                <td style="width: 25%; font-size: 10px;">
                    <strong>Saldo Financiado:</strong><br>
                    <span style="font-size: 12px; font-weight: bold; color: #991b1b;">Bs. {{ number_format($sale->balance_pending, 2) }}</span>
                </td>
                <td style="width: 25%; font-size: 10px;">
                    <strong>Frecuencia / Plazo:</strong><br>
                    <span style="font-size: 11px; font-weight: 600;">{{ $sale->installments_count }} cuotas ({{ $sale->installment_frequency ?? 'MENSUAL' }})</span>
                </td>
                <td style="width: 25%; font-size: 10px;">
                    <strong>Estado del Crédito:</strong><br>
                    @if($sale->credit_status === 'PAGADO')
                        <span style="color: #15803d; font-weight: bold;">TOTALMENTE PAGADO</span>
                    @else
                        <span style="color: #b45309; font-weight: bold;">PENDIENTE DE COBRO</span>
                    @endif
                </td>
            </tr>
        </table>

        @if(isset($installments) && $installments->count() > 0)
        <table class="installments-table">
            <thead>
                <tr>
                    <th>N° Cuota</th>
                    <th>Fecha de Vencimiento</th>
                    <th>Monto Cuota</th>
                    <th>Monto Pagado</th>
                    <th>Saldo Cuota</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($installments as $inst)
                <tr>
                    <td style="font-weight: bold;">Cuota #{{ $inst->installment_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d/m/Y') }}</td>
                    <td style="font-weight: 600;">Bs. {{ number_format($inst->amount, 2) }}</td>
                    <td style="color: #15803d;">Bs. {{ number_format($inst->paid_amount, 2) }}</td>
                    <td style="color: #b91c1c; font-weight: 600;">Bs. {{ number_format(max(0, $inst->amount - $inst->paid_amount), 2) }}</td>
                    <td>
                        @if($inst->status === 'PAGADO')
                            <span style="color: #15803d; font-weight: bold;">PAGADO</span>
                        @else
                            <span style="color: #d97706; font-weight: bold;">PENDIENTE</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    <!-- 6. FIRMAS DE CONFORMIDAD -->
    <table class="signature-table">
        <tr>
            <td style="width: 45%; text-align: center;">
                <div class="signature-line"></div>
                <div class="signature-text">
                    ENTREGUÉ CONFORME<br>
                    <span style="font-weight: normal; font-size: 8.5px;">{{ $sale->user->name }} &bull; Vendedor Autorizado</span>
                </div>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; text-align: center;">
                <div class="signature-line"></div>
                <div class="signature-text">
                    RECIBÍ CONFORME<br>
                    <span style="font-weight: normal; font-size: 8.5px;">{{ $sale->client->name }} &bull; Cliente / CI: {{ $sale->client->ci ?? 'S/N' }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- 7. PIE DE PÁGINA Y CÓDIGO QR -->
    @php
        $qrA4Data = ($activeCompany->name ?? "SISTEMA POS") . " | NOTA DE VENTA N° " . str_pad($sale->id, 6, '0', STR_PAD_LEFT) . 
                    "\nNIT: " . ($activeCompany->nit ?? '1023847029') . 
                    "\nSUCURSAL: " . ($sale->branch->name ?? $activeBranch->name ?? 'Casa Matriz') .
                    "\nFECHA: " . \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') . 
                    "\nCLIENTE: " . ($sale->client->name ?? 'S/N') . " | CI/NIT: " . ($sale->client->ci ?? $sale->client->nit ?? 'S/N') .
                    "\nTOTAL: " . ($activeCompany->currency_symbol ?? 'Bs.') . " " . number_format($sale->total, 2) . 
                    "\nESTADO: " . $sale->status;
        $qrA4Svg = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(110)->margin(0)->errorCorrection('M')->generate($qrA4Data));
    @endphp
    <div class="footer-note">
        <div class="qr-box">
            <img src="data:image/svg+xml;base64,{{ $qrA4Svg }}" alt="Código QR de Verificación">
            <div style="font-size: 9.5px; font-weight: bold; color: #1e293b; margin-top: 2px;">
                *VTA-{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}*
            </div>
        </div>
        <div>{{ $activeCompany->receipt_footer ?? '¡Gracias por su preferencia y confianza! • Documento válido de control interno' }}</div>
    </div>

</body>
</html>
