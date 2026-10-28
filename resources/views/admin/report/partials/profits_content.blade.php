@php
    $sym = $sym ?? ($activeCompany->currency_symbol ?? 'Bs.');
@endphp

<!-- TARJETAS HERO KPI: ANÁLISIS FINANCIERO DE 5 PILARES -->
<div class="row mb-4">
    <!-- 1. VENTAS TOTALES -->
    <div class="col-xl col-md-6 mb-3">
        <div class="profit-hero-card">
            <div>
                <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 11px;">1. Facturación / Ventas</span>
                <h3 class="font-weight-bold text-dark mb-0 mt-1">{{ $sym }} {{ number_format($totalSales, 2) }}</h3>
                <small class="text-muted">{{ $totalOrdersCount }} órdenes/comandas</small>
            </div>
            <div class="profit-hero-icon" style="background: #eef2ff; color: #4f46e5;">
                <i class="fas fa-cash-register"></i>
            </div>
        </div>
    </div>

    <!-- 2. COSTO DE VENTAS (COGS) -->
    <div class="col-xl col-md-6 mb-3">
        <div class="profit-hero-card">
            <div>
                <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 11px;">2. Costo Insumos Vendidos</span>
                <h3 class="font-weight-bold text-danger mb-0 mt-1">{{ $sym }} {{ number_format($totalCogs, 2) }}</h3>
                <small class="text-muted">Costo receta de lo vendido</small>
            </div>
            <div class="profit-hero-icon" style="background: #fee2e2; color: #dc2626;">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
    </div>

    <!-- 3. UTILIDAD BRUTA -->
    <div class="col-xl col-md-6 mb-3">
        <div class="profit-hero-card" style="border-left: 4px solid #10b981 !important;">
            <div>
                <span class="small font-weight-bold text-success text-uppercase d-block" style="font-size: 11px;">3. Utilidad Bruta</span>
                <h3 class="font-weight-bold text-success mb-0 mt-1">{{ $sym }} {{ number_format($grossProfit, 2) }}</h3>
                <span class="badge badge-success font-weight-bold" style="font-size: 11px;">
                    Margen Bruto: {{ $grossMarginPct }}%
                </span>
            </div>
            <div class="profit-hero-icon" style="background: #dcfce7; color: #16a34a;">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>

    <!-- 4. GASTOS OPERATIVOS -->
    <div class="col-xl col-md-6 mb-3">
        <div class="profit-hero-card">
            <div>
                <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 11px;">4. Gastos Caja Chica</span>
                <h3 class="font-weight-bold text-warning mb-0 mt-1">{{ $sym }} {{ number_format($totalExpenses, 2) }}</h3>
                <small class="text-muted">Gas, servicios, insumos menor</small>
            </div>
            <div class="profit-hero-icon" style="background: #fef3c7; color: #d97706;">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- 5. UTILIDAD NETA REAL -->
    <div class="col-xl col-md-6 mb-3">
        <div class="profit-hero-card" style="border-left: 4px solid #059669 !important; background: linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%);">
            <div>
                <span class="small font-weight-bold text-dark text-uppercase d-block" style="font-size: 11px;">5. Utilidad Neta Real</span>
                <h3 class="font-weight-bold text-emerald mb-0 mt-1" style="color: #047857;">{{ $sym }} {{ number_format($netProfit, 2) }}</h3>
                <span class="badge badge-dark font-weight-bold" style="font-size: 11px;">
                    Margen Neto: {{ $netMarginPct }}%
                </span>
            </div>
            <div class="profit-hero-icon" style="background: #047857; color: #fff;">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>
</div>

<!-- BANNER EXPLICATIVO DE RENDIMIENTO FINANCIERO -->
<div class="alert alert-secondary border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap mb-4" style="border-radius: 14px; background: #f8fafc; border-left: 4px solid #6366f1 !important;">
    <div class="d-flex align-items-center">
        <div class="mr-3 text-primary" style="font-size: 28px;">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div>
            <strong class="text-dark d-block">Ecuación Financiera de tu Restaurante en este período:</strong>
            <span class="text-muted" style="font-size: 13px;">
                Facturación (<strong>{{ $sym }} {{ number_format($totalSales, 2) }}</strong>) 
                &minus; Costo Insumos/Recetas (<strong>{{ $sym }} {{ number_format($totalCogs, 2) }}</strong>) 
                = Utilidad Bruta (<strong>{{ $sym }} {{ number_format($grossProfit, 2) }}</strong> | <strong>{{ $grossMarginPct }}%</strong>) 
                &minus; Gastos Operativos (<strong>{{ $sym }} {{ number_format($totalExpenses, 2) }}</strong>) 
                = <strong>Ganancia Neta Disponible: {{ $sym }} {{ number_format($netProfit, 2) }} ({{ $netMarginPct }}% de margen neto)</strong>.
            </span>
        </div>
    </div>
    <div class="mt-2 mt-md-0">
        @if($netProfit > 0)
            <span class="badge badge-success px-3 py-2 font-weight-bold shadow-sm" style="font-size: 13px;">
                <i class="fas fa-arrow-up mr-1"></i> OPERACIÓN RENTABLE (+{{ $netMarginPct }}%)
            </span>
        @else
            <span class="badge badge-danger px-3 py-2 font-weight-bold shadow-sm" style="font-size: 13px;">
                <i class="fas fa-exclamation-triangle mr-1"></i> ATENCIÓN: PÉRDIDA NETA
            </span>
        @endif
    </div>
</div>

<!-- SECCIÓN 1: COMPARATIVO POR SUCURSALES (GLOBAL VS SUCURSALES) -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h5 class="font-weight-bold text-dark mb-0">
                    <i class="fas fa-store-alt text-primary mr-2"></i> Análisis Comparativo por Sucursal
                </h5>
                <small class="text-muted">Desglose de facturación, costos de receta, gastos de caja y ganancia neta por local.</small>
            </div>
            <span class="badge badge-light border text-muted font-weight-bold px-3 py-2">
                {{ count($branchComparison) }} Sucursales Operativas
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="font-weight-bold">Sucursal</th>
                        <th class="text-center font-weight-bold">Órdenes</th>
                        <th class="text-right font-weight-bold">Facturación</th>
                        <th class="text-right font-weight-bold">Costo Insumos (COGS)</th>
                        <th class="text-right font-weight-bold">Utilidad Bruta</th>
                        <th class="text-center font-weight-bold">Margen Bto.</th>
                        <th class="text-right font-weight-bold">Gastos Caja</th>
                        <th class="text-right font-weight-bold text-success">Utilidad Neta</th>
                        <th class="text-center font-weight-bold">Margen Neto</th>
                        <th class="text-center font-weight-bold">% Aporte</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branchComparison as $bData)
                        @php
                            $isHighlighted = (string)$selectedBranchId === (string)$bData['branch_id'];
                        @endphp
                        <tr style="{{ $isHighlighted ? 'background: #eef2ff; font-weight: bold;' : '' }}">
                            <td>
                                <strong class="text-dark">{{ $bData['name'] }}</strong>
                                @if($bData['is_main'])
                                    <span class="badge badge-primary font-weight-bold ml-1" style="font-size: 10px;">MATRIZ</span>
                                @endif
                                @if($isHighlighted)
                                    <span class="badge badge-info ml-1" style="font-size: 10px;">SELECCIONADA</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light border font-weight-bold">{{ $bData['sales_count'] }}</span>
                            </td>
                            <td class="text-right font-weight-bold text-dark">
                                {{ $sym }} {{ number_format($bData['total_sales'], 2) }}
                            </td>
                            <td class="text-right text-danger">
                                {{ $sym }} {{ number_format($bData['cogs'], 2) }}
                            </td>
                            <td class="text-right font-weight-bold text-success">
                                {{ $sym }} {{ number_format($bData['gross_profit'], 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success font-weight-bold">{{ $bData['gross_margin'] }}%</span>
                            </td>
                            <td class="text-right text-warning font-weight-bold">
                                {{ $sym }} {{ number_format($bData['expenses'], 2) }}
                            </td>
                            <td class="text-right font-weight-bold" style="color: #047857; font-size: 14.5px;">
                                {{ $sym }} {{ number_format($bData['net_profit'], 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-dark font-weight-bold">{{ $bData['net_margin'] }}%</span>
                            </td>
                            <td class="text-center" style="width: 140px;">
                                <div class="d-flex align-items-center">
                                    <span class="small font-weight-bold mr-2">{{ $bData['contribution_pct'] }}%</span>
                                    <div class="progress flex-grow-1 progress-thin">
                                        <div class="progress-bar bg-primary" style="width: {{ $bData['contribution_pct'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                No se encontraron ventas para las sucursales en este período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SECCIÓN 2: GRÁFICO TENDENCIA DIARIA + DESGLOSE GASTOS OPERATIVOS -->
<div class="row mb-4">
    <!-- GRÁFICO DIARIO DE VENTAS VS COSTOS Y UTILIDAD -->
    <div class="col-lg-8 mb-3 mb-lg-0">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-chart-area text-primary mr-2"></i> Evolución Diaria: Ventas vs Costos vs Utilidad
                        </h5>
                        <small class="text-muted">Comportamiento diario de la facturación y la ganancia bruta generada.</small>
                    </div>
                </div>

                <div style="height: 320px; position: relative;">
                    <canvas id="profitTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- GASTOS OPERATIVOS POR CATEGORÍA -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-receipt text-warning mr-2"></i> Gastos de Caja Chica
                    </h5>
                    <strong class="text-warning">{{ $sym }} {{ number_format($totalExpenses, 2) }}</strong>
                </div>
                <small class="text-muted d-block mb-3">Servicios básicos, gas, insumos menores y mantenimiento.</small>

                <div class="list-group list-group-flush">
                    @forelse($expensesByCategory as $exp)
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark d-block" style="font-size: 13px;">{{ $exp['category'] }}</strong>
                                <small class="text-muted">{{ $exp['count'] }} comprobantes</small>
                            </div>
                            <span class="font-weight-bold text-danger">{{ $sym }} {{ number_format($exp['total'], 2) }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-2">Sin gastos de caja registrados en el período.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECCIÓN 3: PLATILLOS Y COMBOS MÁS RENTABLES + ABASTECIMIENTO DE PROVEEDORES -->
<div class="row">
    <!-- PLATILLOS Y COMBOS MÁS RENTABLES -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-trophy text-warning mr-2"></i> Ranking de Platillos & Combos Más Rentables
                        </h5>
                        <small class="text-muted">Productos ordenados por mayor ganancia neta en dinero (Venta &minus; Costo receta).</small>
                    </div>
                    <span class="badge badge-light border text-muted font-weight-bold">
                        Top {{ min(10, count($productProfits)) }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="font-weight-bold">Plato / Combo</th>
                                <th class="text-center font-weight-bold">Vendidos</th>
                                <th class="text-right font-weight-bold">Ingresos</th>
                                <th class="text-right font-weight-bold">Costo Insumo</th>
                                <th class="text-right font-weight-bold text-success">Utilidad Neta</th>
                                <th class="text-center font-weight-bold">Margen %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = 0; @endphp
                            @forelse($productProfits as $p)
                                @php
                                    $count++;
                                    if ($count > 10) break;
                                    $pMargin = $p['revenue'] > 0 ? round(($p['profit'] / $p['revenue']) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($p['image'])
                                                <img src="{{ asset('image/' . $p['image']) }}" alt="img" class="rounded mr-2" style="width: 38px; height: 38px; object-fit: cover;">
                                            @else
                                                <div class="rounded bg-light border d-flex align-items-center justify-content-center mr-2" style="width: 38px; height: 38px;">
                                                    <i class="fas fa-utensils text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong class="text-dark d-block" style="font-size: 13.5px;">{{ $p['name'] }}</strong>
                                                <small class="text-muted">
                                                    {{ $p['category'] }} 
                                                    @if($p['item_type'] === 'COMBO')
                                                        &bull; <span class="badge badge-warning text-dark font-weight-bold" style="font-size: 9.5px;">COMBO PROMO</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold">
                                        {{ number_format($p['quantity'], 0) }}
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        {{ $sym }} {{ number_format($p['revenue'], 2) }}
                                    </td>
                                    <td class="text-right text-danger">
                                        {{ $sym }} {{ number_format($p['cost'], 2) }}
                                    </td>
                                    <td class="text-right font-weight-bold text-success" style="font-size: 14px;">
                                        {{ $sym }} {{ number_format($p['profit'], 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success font-weight-bold">
                                            {{ $pMargin }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Sin ventas registradas en este período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ABASTECIMIENTO DE COMPRAS A PROVEEDORES -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-truck-loading text-primary mr-2"></i> Compras por Proveedor
                        </h5>
                        <small class="text-muted">Abastecimiento de carnes, pollos, bebidas y abarrotes.</small>
                    </div>
                    <strong class="text-dark">{{ $sym }} {{ number_format($totalPurchases, 2) }}</strong>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="font-weight-bold">Proveedor</th>
                                <th class="text-center font-weight-bold">Facturas</th>
                                <th class="text-right font-weight-bold">Monto Invertido</th>
                                <th class="text-center font-weight-bold">% Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($providerPurchases as $prov)
                                @php
                                    $pct = $totalPurchases > 0 ? round(($prov['total'] / $totalPurchases) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="text-dark d-block">{{ $prov['name'] }}</strong>
                                        <small class="text-muted">Abastecimiento verificado</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border font-weight-bold">{{ $prov['count'] }}</span>
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        {{ $sym }} {{ number_format($prov['total'], 2) }}
                                    </td>
                                    <td class="text-center" style="width: 100px;">
                                        <span class="badge badge-info font-weight-bold">{{ $pct }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Sin compras registradas a proveedores en este período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

