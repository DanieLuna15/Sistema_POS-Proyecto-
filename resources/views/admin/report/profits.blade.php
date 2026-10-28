@extends('layouts.admin')
@section('title', 'Análisis de Utilidades y Rentabilidad Financiera')

@section('styles')
<style>
    .profit-hero-card {
        border-radius: 16px;
        padding: 18px 20px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .profit-hero-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
    }
    .profit-hero-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .filter-pill-btn {
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 700;
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
        text-decoration: none !important;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .filter-pill-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .filter-pill-btn.active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
    }
    .progress-thin {
        height: 6px;
        border-radius: 3px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(2px);
        z-index: 100;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        transition: opacity 0.2s ease;
    }
</style>
@endsection

@section('content')
@php
    $sym = $sym ?? ($activeCompany->currency_symbol ?? 'Bs.');
@endphp
<div class="content-wrapper">
    <!-- ENCABEZADO Y TÍTULO -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div>
            <span class="badge badge-success font-weight-bold px-3 py-1 mb-1">
                <i class="fas fa-chart-line mr-1"></i> CONTROL FINANCIERO INTEGRAL — COMPRAS VS VENTAS
            </span>
            <h3 class="page-title font-weight-bold mb-0 text-dark">
                Análisis de Utilidades, Ganancias y Rentabilidad
            </h3>
            <small class="text-muted">
                Período analizado: <strong id="lbl_period_label">{{ $periodLabel }}</strong> &bull; Ámbito: 
                <span id="lbl_branch_scope" class="badge {{ $selectedBranchId === 'ALL' ? 'badge-primary' : 'badge-info' }}">
                    {{ $selectedBranchId === 'ALL' ? '🌐 Consolidado Global (Todas las Sucursales)' : optional($branches->firstWhere('id', $selectedBranchId))->name }}
                </span>
            </small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap" style="gap: 8px;">
            <a href="{{ route('restaurant.index') }}" class="btn btn-outline-dark btn-sm font-weight-bold">
                <i class="fas fa-utensils mr-1"></i> Módulo Restaurante
            </a>
            <a href="{{ route('products.index', ['type' => 'COMBO']) }}" class="btn btn-outline-warning text-dark btn-sm font-weight-bold">
                <i class="fas fa-gifts mr-1 text-danger"></i> Margen de Combos
            </a>
            <a href="{{ route('reports.date') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
                <i class="fas fa-calendar mr-1"></i> Reporte Diario
            </a>
        </div>
    </div>

    <!-- BARRA DE FILTROS INTELIGENTE (SUCURSAL Y PERÍODO CON AJAX) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-3">
            <form action="{{ route('reports.profits') }}" method="GET" class="row align-items-end" id="profitsFilterForm" onsubmit="event.preventDefault(); applyCustomDateFilter();">
                <!-- SUCURSAL -->
                <div class="col-lg-3 col-md-6 form-group mb-2 mb-lg-0">
                    <label class="small font-weight-bold text-dark mb-1">
                        <i class="fas fa-store-alt text-primary mr-1"></i> Sucursal a Analizar:
                    </label>
                    <select name="branch_id" id="filter_branch_id" class="form-control form-control-sm font-weight-bold" onchange="onBranchChange(this.value)">
                        <option value="ALL" {{ $selectedBranchId === 'ALL' ? 'selected' : '' }}>
                            🌐 Consolidado Global (Todas las Sucursales)
                        </option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ (string)$selectedBranchId === (string)$b->id ? 'selected' : '' }}>
                                {{ $b->name }} {{ $b->is_main ? '(Matriz)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- PREAJUSTES DE PERÍODO -->
                <div class="col-lg-5 col-md-6 form-group mb-2 mb-lg-0">
                    <label class="small font-weight-bold text-dark mb-1 d-block">
                        <i class="fas fa-calendar-alt text-primary mr-1"></i> Período de Tiempo:
                    </label>
                    <div class="d-flex flex-wrap" style="gap: 6px;">
                        <button type="button" onclick="selectPeriod('HOY')" data-period="HOY" class="filter-pill-btn {{ $period === 'HOY' ? 'active' : '' }}">Hoy</button>
                        <button type="button" onclick="selectPeriod('SEMANA')" data-period="SEMANA" class="filter-pill-btn {{ $period === 'SEMANA' ? 'active' : '' }}">Semana</button>
                        <button type="button" onclick="selectPeriod('MES')" data-period="MES" class="filter-pill-btn {{ $period === 'MES' ? 'active' : '' }}">Mes Actual</button>
                        <button type="button" onclick="selectPeriod('TRIMESTRE')" data-period="TRIMESTRE" class="filter-pill-btn {{ $period === 'TRIMESTRE' ? 'active' : '' }}">3 Meses</button>
                        <button type="button" onclick="selectPeriod('ANIO')" data-period="ANIO" class="filter-pill-btn {{ $period === 'ANIO' ? 'active' : '' }}">Año {{ date('Y') }}</button>
                    </div>
                </div>

                <!-- RANGO PERSONALIZADO -->
                <div class="col-lg-4 form-group mb-0">
                    <label class="small font-weight-bold text-dark mb-1">
                        <i class="fas fa-sliders-h text-primary mr-1"></i> O Rango de Fechas:
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="start_date" id="filter_start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        <div class="input-group-prepend input-group-append">
                            <span class="input-group-text bg-light">al</span>
                        </div>
                        <input type="date" name="end_date" id="filter_end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        <div class="input-group-append">
                            <button type="button" id="btn_custom_filter" onclick="applyCustomDateFilter()" class="btn btn-primary font-weight-bold">
                                <i class="fas fa-filter mr-1"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- CONTENEDOR DINÁMICO CON CARGA AJAX -->
    <div id="profitsDynamicContainerWrapper" style="position: relative; min-height: 400px;">
        <!-- OVERLAY DE CARGA AJAX -->
        <div id="profitsLoadingOverlay" class="loading-overlay" style="display: none;">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <strong class="text-dark h5 mb-1 font-weight-bold">Actualizando Reporte Financiero...</strong>
            <small class="text-muted">Calculando ventas, recetas COGS, utilidades y gastos en tiempo real...</small>
        </div>

        <div id="profitsDynamicContainer">
            @include('admin.report.partials.profits_content')
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! Html::script('melody/vendors/chart.js/Chart.min.js') !!}
<script>
    var currentPeriod = "{{ $period }}";
    var currentBranchId = "{{ $selectedBranchId }}";
    var sym = "{{ $sym }}";
    var profitChartInstance = null;

    function renderProfitChart(chartData) {
        var canvas = document.getElementById('profitTrendChart');
        if (!canvas) return;

        if (profitChartInstance) {
            profitChartInstance.destroy();
            profitChartInstance = null;
        }

        if (!chartData || chartData.length === 0) return;

        var labels = chartData.map(function(item) { return item.date; });
        var salesData = chartData.map(function(item) { return item.sales; });
        var cogsData = chartData.map(function(item) { return item.cogs; });
        var profitData = chartData.map(function(item) { return item.profit; });

        var ctx = canvas.getContext('2d');
        profitChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ventas Brutas (' + sym + ')',
                        data: salesData,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Utilidad Bruta (' + sym + ')',
                        data: profitData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Costo Insumos (COGS) (' + sym + ')',
                        data: cogsData,
                        borderColor: '#ef4444',
                        backgroundColor: 'transparent',
                        borderWidth: 1.8,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return sym + ' ' + value;
                            }
                        },
                        gridLines: {
                            color: '#f1f5f9'
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(item, data) {
                            return data.datasets[item.datasetIndex].label + ': ' + sym + ' ' + parseFloat(item.yLabel).toFixed(2);
                        }
                    }
                }
            }
        });
    }

    function fetchProfitsAjax(params) {
        var overlay = document.getElementById('profitsLoadingOverlay');
        if (overlay) overlay.style.display = 'flex';

        // Actualizar URL en historial sin recarga completa
        var searchParams = new URLSearchParams(params);
        window.history.replaceState({}, '', window.location.pathname + '?' + searchParams.toString());

        $.ajax({
            url: "{{ route('reports.profits') }}",
            type: 'GET',
            data: params,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#profitsDynamicContainer').html(response.html);

                    // Actualizar subtítulo y badges de período y sucursal
                    $('#lbl_period_label').text(response.periodLabel);
                    var scopeBadge = $('#lbl_branch_scope');
                    scopeBadge.text(response.selectedBranchName);
                    if (response.selectedBranchId === 'ALL') {
                        scopeBadge.removeClass('badge-info').addClass('badge-primary');
                    } else {
                        scopeBadge.removeClass('badge-primary').addClass('badge-info');
                    }

                    // Actualizar botones de período
                    $('.filter-pill-btn').removeClass('active');
                    if (response.period && response.period !== 'CUSTOM') {
                        $('.filter-pill-btn[data-period="' + response.period + '"]').addClass('active');
                    }

                    // Renderizar el gráfico con los nuevos datos
                    renderProfitChart(response.chartData);
                }
            },
            error: function(xhr) {
                console.error("Error al actualizar reporte vía AJAX:", xhr);
                alert("Ocurrió un error al cargar los datos del reporte. Por favor intente nuevamente.");
            },
            complete: function() {
                if (overlay) overlay.style.display = 'none';
            }
        });
    }

    function onBranchChange(branchId) {
        currentBranchId = branchId;
        var params = {
            branch_id: currentBranchId,
            period: currentPeriod
        };
        if (currentPeriod === 'CUSTOM') {
            params.start_date = $('#filter_start_date').val();
            params.end_date = $('#filter_end_date').val();
        }
        fetchProfitsAjax(params);
    }

    function selectPeriod(period) {
        currentPeriod = period;
        var params = {
            branch_id: currentBranchId,
            period: currentPeriod
        };
        fetchProfitsAjax(params);
    }

    function applyCustomDateFilter() {
        currentPeriod = 'CUSTOM';
        var startDate = $('#filter_start_date').val();
        var endDate = $('#filter_end_date').val();

        if (!startDate || !endDate) {
            alert('Por favor ingrese tanto la fecha inicial como la fecha final.');
            return;
        }

        var params = {
            branch_id: currentBranchId,
            period: 'CUSTOM',
            start_date: startDate,
            end_date: endDate
        };
        fetchProfitsAjax(params);
    }

    // Inicializar el gráfico inicial en la primera carga
    $(document).ready(function() {
        var initialChartData = {!! json_encode($chartData ?? []) !!};
        renderProfitChart(initialChartData);
    });
</script>
@endsection
