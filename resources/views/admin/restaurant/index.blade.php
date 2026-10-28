@extends('layouts.admin')
@section('title', 'Mapa de Mesas y Salón — ' . ($activeCompany->name ?? 'Restaurante'))

@section('styles')
<style>
    /* Estadísticas superiores */
    .stat-pill {
        border-radius: 14px;
        padding: 14px 18px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Pestañas de Zonas / Áreas del Restaurante */
    .zone-tabs-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        background: #f8fafc;
        padding: 10px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .zone-tab-btn {
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 700;
        font-size: 13px;
        border: 1px solid transparent;
        background: #fff;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    .zone-tab-btn:hover {
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .zone-tab-btn.active {
        background: #4f46e5;
        color: #fff;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
    }

    /* LIENZO / PLANO 2D INTERACTIVO DEL SALÓN */
    .floor-canvas-wrapper {
        position: relative;
        width: 100%;
        height: 560px;
        background-color: #f8fafc;
        background-image:
            linear-gradient(to right, rgba(203, 213, 225, 0.45) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(203, 213, 225, 0.45) 1px, transparent 1px);
        background-size: 30px 30px;
        border: 2px solid #cbd5e1;
        border-radius: 16px;
        overflow: auto;
        box-shadow: inset 0 2px 10px rgba(15, 23, 42, 0.04);
    }
    .floor-canvas-wrapper.design-mode {
        border: 2px dashed #4f46e5;
        background-color: #eef2ff;
        background-image:
            linear-gradient(to right, rgba(99, 102, 241, 0.2) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(99, 102, 241, 0.2) 1px, transparent 1px);
    }

    /* Elementos arquitectónicos decorativos del plano */
    .floor-landmark {
        position: absolute;
        padding: 5px 14px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        pointer-events: none;
        z-index: 1;
    }
    .landmark-entrance {
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: #e2e8f0;
        color: #475569;
        border: 1px solid #94a3b8;
    }
    .landmark-kitchen {
        top: 10px;
        right: 20px;
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }
    .landmark-cashier {
        top: 10px;
        left: 20px;
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #60a5fa;
    }

    /* Nodos de Mesas en el Mapa 2D */
    .map-table-node {
        position: absolute;
        width: 145px;
        min-height: 115px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        text-align: center;
        cursor: pointer;
        user-select: none;
        transition: box-shadow 0.2s ease, transform 0.15s ease;
        z-index: 5;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.10);
    }
    .map-table-node:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
        z-index: 10;
    }

    /* Formas geométricas de las mesas */
    .map-table-node.shape-CUADRADA {
        border-radius: 16px;
        width: 145px;
        min-height: 120px;
    }
    .map-table-node.shape-REDONDA {
        border-radius: 50%;
        width: 138px;
        height: 138px;
        justify-content: center;
    }
    .map-table-node.shape-RECTANGULAR {
        border-radius: 16px;
        width: 195px;
        min-height: 115px;
    }
    .map-table-node.shape-BARRA {
        border-radius: 10px;
        width: 130px;
        min-height: 95px;
    }

    /* Sillas decorativas alrededor de la mesa */
    .map-table-node::before,
    .map-table-node::after {
        content: '';
        position: absolute;
        background: rgba(100, 116, 139, 0.28);
        border-radius: 4px;
        pointer-events: none;
    }
    .map-table-node::before {
        top: -6px;
        left: 25%;
        width: 50%;
        height: 4px;
    }
    .map-table-node::after {
        bottom: -6px;
        left: 25%;
        width: 50%;
        height: 4px;
    }

    /* Estados de color de la mesa */
    .table-state-LIBRE {
        background: linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%);
        border: 3px solid #22c55e;
        color: #14532d;
    }
    .table-state-OCUPADA {
        background: linear-gradient(145deg, #fff1f2 0%, #ffe4e6 100%);
        border: 3px solid #ef4444;
        color: #7f1d1d;
    }
    .table-state-LISTO {
        background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 100%);
        border: 3px solid #2563eb;
        color: #1e3a8a;
        animation: pulseReady 2s infinite;
    }
    .table-state-CUENTA_PEDIDA {
        background: linear-gradient(145deg, #fffbeb 0%, #fef3c7 100%);
        border: 3px solid #f59e0b;
        color: #78350f;
    }

    @keyframes pulseReady {
        0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.45); }
        70% { box-shadow: 0 0 0 12px rgba(37, 99, 235, 0); }
        100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
    }

    .design-mode .map-table-node {
        cursor: move !important;
        border-style: dashed !important;
    }
    .design-handle-bar {
        display: none;
        width: 100%;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 3px;
        border-bottom: 1px dashed rgba(0,0,0,0.15);
        margin-bottom: 4px;
    }
    .design-mode .design-handle-bar {
        display: flex;
    }

    /* Tarjetas en Vista Cuadrícula */
    .grid-table-card {
        border-radius: 16px;
        padding: 16px;
        transition: all 0.2s ease;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    }
    .grid-table-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
    }

    /* Cuadros contenedores por Área / Ambiente (evita sobreposición en "Ver Todas") */
    .zone-floor-box {
        border: 2px solid #cbd5e1;
        border-radius: 16px;
        background: #ffffff;
        margin-bottom: 22px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    }
    .zone-floor-header {
        background: linear-gradient(90deg, #1e1b4b 0%, #312e81 100%);
        color: #fff;
        padding: 10px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    .zone-floor-body {
        padding: 12px;
        background: #f8fafc;
    }
</style>
@endsection

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
    $groupedTables = $tables->groupBy('zone');
    $zonesList = $groupedTables->keys()->toArray();
    if (empty($zonesList)) {
        $zonesList = ['Salón Principal'];
    }
    $activeZoneRequest = request('zone', $zonesList[0] ?? 'Salón Principal');
    if (!in_array($activeZoneRequest, $zonesList) && $activeZoneRequest !== 'TODAS') {
        $activeZoneRequest = $zonesList[0];
    }

    $freeCount = $tables->where('status', 'LIBRE')->count();
    $busyCount = $tables->where('status', '!=', 'LIBRE')->count();
    $readyOrdersCount = $kitchenOrders->where('status', 'LISTO')->count();
@endphp

<div class="content-wrapper">
    <!-- CABECERA DEL SALÓN GASTRONÓMICO -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div>
            <span class="badge badge-danger px-2 py-1 mb-1 font-weight-bold">
                <i class="fas fa-utensils mr-1"></i> MÓDULO GASTRONÓMICO PROFESIONAL — {{ strtoupper($branch->name ?? 'CASA MATRIZ') }}
            </span>
            <h3 class="page-title font-weight-bold mb-0 text-dark">
                Mapa Visual de Mesas, Áreas y Comandas
            </h3>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap" style="gap: 8px;">
            <a href="{{ route('restaurant.kitchen') }}" class="btn btn-warning text-dark font-weight-bold shadow-sm">
                <i class="fas fa-fire-alt mr-1 text-danger"></i> Pantalla Exclusiva de Cocina (KDS)
                @if($kitchenOrders->count() > 0)
                    <span class="badge badge-danger ml-1">{{ $kitchenOrders->count() }}</span>
                @endif
            </a>
            <button type="button" class="btn btn-info font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalExternalOrder">
                <i class="fas fa-motorcycle mr-1"></i> + Para Llevar / Delivery
            </button>
            <button type="button" class="btn btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalNewTable">
                <i class="fas fa-plus-circle mr-1"></i> + Nueva Mesa o Área
            </button>
            <a href="{{ route('products.index', ['type' => 'COMBO']) }}" class="btn btn-outline-primary font-weight-bold shadow-sm bg-white">
                <i class="fas fa-gifts text-danger mr-1"></i> Administrar Combos
            </a>
            <a href="{{ route('sales.pos') }}" class="btn btn-dark font-weight-bold shadow-sm">
                <i class="fas fa-cash-register text-warning mr-1"></i> Caja Rápida
            </a>
        </div>
    </div>

    <!-- RESUMEN RÁPIDO DEL SALÓN -->
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-2">
            <div class="stat-pill">
                <div>
                    <small class="text-muted font-weight-bold d-block">MESAS LIBRES</small>
                    <h4 class="font-weight-bold text-success mb-0">{{ $freeCount }} disponibles</h4>
                </div>
                <i class="fas fa-check-circle fa-2x text-success"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="stat-pill">
                <div>
                    <small class="text-muted font-weight-bold d-block">MESAS OCUPADAS</small>
                    <h4 class="font-weight-bold text-danger mb-0">{{ $busyCount }} en atención</h4>
                </div>
                <i class="fas fa-utensils fa-2x text-danger"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="stat-pill">
                <div>
                    <small class="text-muted font-weight-bold d-block">PEDIDOS EN COCINA</small>
                    <h4 class="font-weight-bold text-warning mb-0">{{ $kitchenOrders->count() }} comandas</h4>
                </div>
                <i class="fas fa-fire fa-2x text-warning"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="stat-pill">
                <div>
                    <small class="text-muted font-weight-bold d-block">LLEVAR / DELIVERY</small>
                    <h4 class="font-weight-bold text-info mb-0">{{ $externalOrders->count() }} activos</h4>
                </div>
                <i class="fas fa-shopping-bag fa-2x text-info"></i>
            </div>
        </div>
    </div>

    <!-- BARRA DE ÁREAS / AMBIENTES Y CONTROLES DEL DISEÑADOR DE PLANO 2D -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 10px;">
                <!-- PESTAÑAS DE ÁREAS / AMBIENTES -->
                <div class="zone-tabs-bar">
                    @foreach($zonesList as $zName)
                        @php
                            $zTables = $groupedTables[$zName] ?? collect();
                            $zBusy = $zTables->where('status', '!=', 'LIBRE')->count();
                        @endphp
                        <button type="button"
                                class="zone-tab-btn {{ $activeZoneRequest === $zName ? 'active' : '' }}"
                                onclick="switchZone('{{ addslashes($zName) }}', this)">
                            <i class="fas fa-layer-group mr-1"></i> {{ $zName }}
                            <span class="badge {{ $zBusy > 0 ? 'badge-danger' : 'badge-light text-dark' }} ml-1">
                                {{ $zBusy }}/{{ $zTables->count() }}
                            </span>
                        </button>
                    @endforeach
                    <button type="button"
                            class="zone-tab-btn {{ $activeZoneRequest === 'TODAS' ? 'active' : '' }}"
                            onclick="switchZone('TODAS', this)">
                        <i class="fas fa-th-large mr-1"></i> Ver Todas ({{ $tables->count() }})
                    </button>
                </div>

                <!-- CONTROLES DE MODO PLANO 2D Y DISEÑADOR DRAG & DROP -->
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" id="btnViewMap" class="btn btn-primary font-weight-bold" onclick="setViewMode('MAP')">
                            <i class="fas fa-map-marked-alt mr-1"></i> Plano 2D Interactivo
                        </button>
                        <button type="button" id="btnViewGrid" class="btn btn-outline-primary font-weight-bold" onclick="setViewMode('GRID')">
                            <i class="fas fa-th mr-1"></i> Vista Cuadrícula
                        </button>
                    </div>

                    <button type="button" id="btnToggleDesigner" class="btn btn-sm btn-outline-dark font-weight-bold" onclick="toggleDesignMode()">
                        <i class="fas fa-arrows-alt mr-1 text-primary"></i> <span id="lblDesignMode">Diseñar / Mover Mesas</span>
                    </button>
                </div>
            </div>

            <!-- AVISO CUANDO EL MODO DISEÑADOR ESTÁ ACTIVO -->
            <div id="designerBanner" class="alert alert-primary py-2 px-3 mb-3" style="display: none;">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <i class="fas fa-hand-paper mr-2"></i>
                        <strong>Modo Diseñador de Salón Activo:</strong> Arrastra cualquier mesa con el mouse o dedo para ubicarla en el plano de tu restaurante. Los cambios se guardan automáticamente.
                    </div>
                    <button type="button" class="btn btn-xs btn-primary font-weight-bold" onclick="toggleDesignMode()">
                        <i class="fas fa-check mr-1"></i> Terminar Diseño
                    </button>
                </div>
            </div>

            <!-- LEYENDA DE COLORES -->
            <div class="d-flex align-items-center flex-wrap small text-muted mb-2" style="gap: 16px;">
                <span><span class="badge badge-success">&nbsp;</span> <strong>Verde:</strong> Mesa Libre (Clic para abrir)</span>
                <span><span class="badge badge-danger">&nbsp;</span> <strong>Rojo:</strong> Mesa Ocupada (En consumo)</span>
                <span><span class="badge badge-primary">&nbsp;</span> <strong>Azul:</strong> ¡Platos Listos en Cocina!</span>
                <span><span class="badge badge-warning">&nbsp;</span> <strong>Ámbar:</strong> Pre-Cuenta impresa / Por cobrar</span>
            </div>

            <!-- ========================================================================= -->
            <!-- VISTA 1: MAPA / PLANO 2D INTERACTIVO SEPARADO POR CUADROS DE ÁREA         -->
            <!-- ========================================================================= -->
            <div id="mapContainerView">
                @foreach($groupedTables as $zoneName => $zoneTables)
                    @php
                        $showZoneBox = ($activeZoneRequest === 'TODAS' || $activeZoneRequest === $zoneName);
                        $zoneFree = $zoneTables->where('status', 'LIBRE')->count();
                        $zoneBusy = $zoneTables->where('status', '!=', 'LIBRE')->count();
                        // Calcular altura dinámica según la posición más baja de las mesas del área
                        $maxPosY = $zoneTables->max('pos_y') ?: 180;
                        $canvasMinHeight = max(360, intval($maxPosY) + 170);
                    @endphp
                    <div class="zone-floor-box"
                         data-zone-box="{{ $zoneName }}"
                         style="{{ $showZoneBox ? '' : 'display:none;' }}">

                        <!-- CABECERA DEL CUADRO DE ÁREA -->
                        <div class="zone-floor-header">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                                <h5 class="mb-0 font-weight-bold text-white">
                                    <i class="fas fa-map-signs text-warning mr-2"></i> Área: {{ $zoneName }}
                                </h5>
                                <span class="badge badge-success px-2 py-1">{{ $zoneFree }} Libres</span>
                                @if($zoneBusy > 0)
                                    <span class="badge badge-danger px-2 py-1">{{ $zoneBusy }} Ocupadas</span>
                                @endif
                                <span class="badge badge-light text-dark px-2 py-1">{{ $zoneTables->count() }} mesas en total</span>
                            </div>
                            <button type="button" class="btn btn-xs btn-light font-weight-bold text-primary"
                                    onclick="openNewTableInZone('{{ addslashes($zoneName) }}')">
                                <i class="fas fa-plus-circle mr-1"></i> + Agregar Mesa en {{ $zoneName }}
                            </button>
                        </div>

                        <!-- LIENZO 2D EXCLUSIVO DE ESTA ÁREA (SIN SOBREPOSICIÓN ENTRE ÁREAS) -->
                        <div class="zone-floor-body">
                            <div class="floor-canvas-wrapper floor-canvas-area" style="min-height: {{ $canvasMinHeight }}px;">
                                <!-- Puntos de referencia arquitectónicos del área -->
                                <div class="floor-landmark landmark-cashier"><i class="fas fa-compass mr-1"></i> Ambiente: {{ $zoneName }}</div>
                                <div class="floor-landmark landmark-kitchen"><i class="fas fa-concierge-bell mr-1"></i> Zona de Servicio / Pasillo</div>
                                <div class="floor-landmark landmark-entrance"><i class="fas fa-door-open mr-1"></i> Acceso a {{ $zoneName }}</div>

                                @foreach($zoneTables as $table)
                                    @php
                                        $order = $table->activeOrder;
                                        $visualState = $table->status;
                                        if ($order && $order->status === 'LISTO') {
                                            $visualState = 'LISTO';
                                        } elseif ($order && $table->status === 'LIBRE') {
                                            $visualState = 'OCUPADA';
                                        }
                                        $shape = $table->shape ?: 'CUADRADA';
                                        $posX = intval($table->pos_x ?: 40);
                                        $posY = intval($table->pos_y ?: 55);
                                    @endphp

                                    <div class="map-table-node shape-{{ $shape }} table-state-{{ $visualState }}"
                                         id="map_table_{{ $table->id }}"
                                         data-id="{{ $table->id }}"
                                         data-zone="{{ $table->zone }}"
                                         data-name="{{ $table->name }}"
                                         data-capacity="{{ $table->capacity }}"
                                         data-shape="{{ $shape }}"
                                         data-status="{{ $table->status }}"
                                         style="left: {{ $posX }}px; top: {{ $posY }}px;"
                                         onclick="handleTableClick({{ $table->id }})">

                                        <!-- Barra de herramientas rápida en Modo Diseñador -->
                                        <div class="design-handle-bar" onclick="event.stopPropagation();">
                                            <span class="badge badge-dark" style="font-size:9px;"><i class="fas fa-arrows-alt"></i> Mover</span>
                                            <div>
                                                <button type="button" class="btn btn-xs btn-light p-0 px-1" title="Editar mesa"
                                                        onclick="openEditTableModal({{ $table->id }}, '{{ addslashes($table->name) }}', '{{ addslashes($table->zone) }}', {{ $table->capacity }}, '{{ $shape }}')">
                                                    <i class="fas fa-edit text-primary" style="font-size:10px;"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Nombre y Capacidad de la Mesa -->
                                        <div>
                                            <div class="font-weight-bold" style="font-size: 14px; line-height: 1.15;">
                                                {{ $table->name }}
                                            </div>
                                            <small class="d-block opacity-75" style="font-size: 10px;">
                                                <i class="fas fa-users"></i> {{ $table->capacity }} pers.
                                            </small>
                                        </div>

                                        <!-- Estado y Consumo en Vivo -->
                                        @if($table->status === 'LIBRE' && !$order)
                                            <span class="badge badge-success px-2 py-1 mt-1" style="font-size: 10px;">
                                                LIBRE
                                            </span>
                                        @else
                                            <div class="mt-1">
                                                @if($visualState === 'LISTO')
                                                    <span class="badge badge-primary px-2 py-1 d-block mb-1" style="font-size: 9px;">
                                                        🔔 ¡PLATO LISTO!
                                                    </span>
                                                @elseif($table->status === 'CUENTA_PEDIDA')
                                                    <span class="badge badge-warning text-dark px-2 py-1 d-block mb-1" style="font-size: 9px;">
                                                        🧾 PRE-CUENTA
                                                    </span>
                                                @endif
                                                <div class="font-weight-bold" style="font-size: 13px;">
                                                    {{ $sym }} {{ number_format($order->total ?? 0, 2) }}
                                                </div>
                                                @if($order && $order->opened_at)
                                                    <small class="d-block" style="font-size: 9px;">
                                                        <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($order->opened_at)->diffForHumans(null, true) }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Formulario oculto para abrir/continuar mesa al hacer clic -->
                                        <form id="open_table_form_{{ $table->id }}" action="{{ route('restaurant.tables.open', $table) }}" method="POST" style="display:none;">
                                            @csrf
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ========================================================================= -->
            <!-- VISTA 2: CUADRÍCULA ORGANIZADA EN CUADROS POR ÁREAS                       -->
            <!-- ========================================================================= -->
            <div id="gridContainerView" style="display: none;">
                @foreach($groupedTables as $zoneName => $zoneTables)
                    @php
                        $showZoneBox = ($activeZoneRequest === 'TODAS' || $activeZoneRequest === $zoneName);
                    @endphp
                    <div class="zone-floor-box"
                         data-zone-box="{{ $zoneName }}"
                         style="{{ $showZoneBox ? '' : 'display:none;' }}">
                        <div class="zone-floor-header">
                            <h5 class="mb-0 font-weight-bold text-white">
                                <i class="fas fa-th-large text-warning mr-2"></i> Área: {{ $zoneName }} ({{ $zoneTables->count() }} mesas)
                            </h5>
                            <button type="button" class="btn btn-xs btn-light font-weight-bold text-primary"
                                    onclick="openNewTableInZone('{{ addslashes($zoneName) }}')">
                                <i class="fas fa-plus-circle mr-1"></i> + Nueva Mesa
                            </button>
                        </div>
                        <div class="zone-floor-body">
                            <div class="row">
                                @foreach($zoneTables as $table)
                                    @php
                                        $order = $table->activeOrder;
                                        $visualState = $table->status;
                                        if ($order && $order->status === 'LISTO') {
                                            $visualState = 'LISTO';
                                        } elseif ($order && $table->status === 'LIBRE') {
                                            $visualState = 'OCUPADA';
                                        }
                                    @endphp
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <div class="grid-table-card table-state-{{ $visualState }}">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div>
                                                        <span class="badge badge-dark mb-1" style="font-size:10px;">{{ $table->zone }}</span>
                                                        <h5 class="font-weight-bold mb-0">{{ $table->name }}</h5>
                                                    </div>
                                                    <button type="button" class="btn btn-xs btn-light border"
                                                            onclick="openEditTableModal({{ $table->id }}, '{{ addslashes($table->name) }}', '{{ addslashes($table->zone) }}', {{ $table->capacity }}, '{{ $table->shape ?: 'CUADRADA' }}')">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                </div>
                                                <small class="d-block mb-2"><i class="fas fa-users mr-1"></i> Capacidad: {{ $table->capacity }} personas</small>

                                                @if($order)
                                                    <div class="p-2 rounded bg-white border mb-2 text-dark">
                                                        <div class="d-flex justify-content-between small">
                                                            <strong>{{ $order->order_number }}</strong>
                                                            <span>{{ $order->items->sum('quantity') }} platos</span>
                                                        </div>
                                                        <div class="h5 font-weight-bold text-danger mb-0 mt-1">
                                                            {{ $sym }} {{ number_format($order->total, 2) }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <form action="{{ route('restaurant.tables.open', $table) }}" method="POST">
                                                @csrf
                                                @if($table->status === 'LIBRE' && !$order)
                                                    <button type="submit" class="btn btn-success btn-sm btn-block font-weight-bold">
                                                        <i class="fas fa-door-open mr-1"></i> Ocupar Mesa / Comandar
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-danger btn-sm btn-block font-weight-bold">
                                                        <i class="fas fa-utensils mr-1"></i> Ver Comanda / Cobrar
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- PEDIDOS ACTIVOS PARA LLEVAR Y DELIVERY -->
    @if($externalOrders->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title font-weight-bold text-info mb-3">
                <i class="fas fa-motorcycle mr-2"></i> Pedidos Activos: Para Llevar y Delivery ({{ $externalOrders->count() }})
            </h5>
            <div class="row">
                @foreach($externalOrders as $ext)
                    <div class="col-md-4 mb-3">
                        <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge {{ $ext->service_type === 'DELIVERY' ? 'badge-info' : 'badge-warning text-dark' }} mb-1">
                                    {{ $ext->service_type }} — {{ $ext->order_number }}
                                </span>
                                @if($ext->status === 'LISTO')
                                    <span class="badge badge-success ml-1">🔔 ¡LISTO EN COCINA!</span>
                                @endif
                                <h6 class="font-weight-bold mb-1 text-dark">{{ $ext->customer_name }}</h6>
                                @if($ext->delivery_address)
                                    <small class="text-muted d-block"><i class="fas fa-map-marker-alt text-danger"></i> {{ $ext->delivery_address }}</small>
                                @endif
                                <strong class="text-primary">{{ $sym }} {{ number_format($ext->total, 2) }}</strong>
                            </div>
                            <a href="{{ route('restaurant.orders.show', $ext) }}" class="btn btn-primary btn-sm font-weight-bold">
                                Abrir / Cobrar <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- MODAL: CREAR NUEVA MESA O NUEVA ÁREA/AMBIENTE -->
<div class="modal fade" id="modalNewTable" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-chair mr-1"></i> Agregar Mesa o Nueva Área al Salón</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('restaurant.tables.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre / Número de Mesa *</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Mesa 7, VIP 1, Jardín 2" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Área / Ambiente del Restaurante *</label>
                        <select name="zone" id="new_table_zone_select" class="form-control" onchange="toggleCustomZoneInput(this.value, 'new_custom_zone_box')">
                            @foreach($zonesList as $z)
                                <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                            <option value="Terraza / Exterior">Terraza / Exterior</option>
                            <option value="Salón VIP">Salón VIP</option>
                            <option value="Barra Rápida">Barra Rápida</option>
                            <option value="Segundo Piso">Segundo Piso</option>
                            <option value="__NEW__">+ Crear Nueva Área Personalizada...</option>
                        </select>
                    </div>

                    <div class="form-group" id="new_custom_zone_box" style="display: none;">
                        <label class="font-weight-bold text-primary">Nombre de la Nueva Área / Ambiente *</label>
                        <input type="text" name="custom_zone" id="new_custom_zone_input" class="form-control border-primary" placeholder="Ej: Patio Trasero, Rooftop, Salón Eventos...">
                    </div>

                    <div class="row">
                        <div class="col-6 form-group">
                            <label class="font-weight-bold">Capacidad (Sillas) *</label>
                            <input type="number" name="capacity" class="form-control" value="4" min="1" max="30" required>
                        </div>
                        <div class="col-6 form-group">
                            <label class="font-weight-bold">Forma en el Mapa 2D *</label>
                            <select name="shape" class="form-control">
                                <option value="CUADRADA">⬛ Cuadrada (Estándar)</option>
                                <option value="REDONDA">⚪ Redonda / Café</option>
                                <option value="RECTANGULAR">▬ Rectangular (Familiar)</option>
                                <option value="BARRA">🍸 Barra / Taburete</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Guardar en el Mapa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR O ELIMINAR MESA -->
<div class="modal fade" id="modalEditTable" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Configurar Mesa</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="editTableForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre de la Mesa *</label>
                        <input type="text" name="name" id="edit_table_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Área / Ambiente *</label>
                        <input type="text" name="custom_zone" id="edit_table_zone" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label class="font-weight-bold">Capacidad (Personas)</label>
                            <input type="number" name="capacity" id="edit_table_capacity" class="form-control" min="1" max="50" required>
                        </div>
                        <div class="col-6 form-group">
                            <label class="font-weight-bold">Forma Visual en el Mapa</label>
                            <select name="shape" id="edit_table_shape" class="form-control">
                                <option value="CUADRADA">⬛ Cuadrada</option>
                                <option value="REDONDA">⚪ Redonda</option>
                                <option value="RECTANGULAR">▬ Rectangular</option>
                                <option value="BARRA">🍸 Barra</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" onclick="deleteSelectedTable()">
                        <i class="fas fa-trash-alt mr-1"></i> Eliminar Mesa
                    </button>
                    <div>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary font-weight-bold">Guardar Cambios</button>
                    </div>
                </div>
            </form>
            <form id="deleteTableForm" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<!-- MODAL: NUEVO PEDIDO PARA LLEVAR O DELIVERY -->
<div class="modal fade" id="modalExternalOrder" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-motorcycle mr-1"></i> Nuevo Pedido Para Llevar / Delivery</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('restaurant.orders.external') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Modalidad de Servicio *</label>
                        <select name="service_type" class="form-control font-weight-bold" required>
                            <option value="LLEVAR">🛍️ Para Llevar (Recojo en Mostrador)</option>
                            <option value="DELIVERY">🛵 Delivery (Envío a Domicilio)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre del Cliente *</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Ej: Juan Pérez" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Teléfono / WhatsApp</label>
                        <input type="text" name="delivery_phone" class="form-control" placeholder="Ej: 71234567">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Dirección de Entrega (Si es Delivery)</label>
                        <input type="text" name="delivery_address" class="form-control" placeholder="Ej: Av. América esq. Libertador #450">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Crear Comanda y Elegir Platos</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let isDesignMode = false;
    let currentZone = "{{ addslashes($activeZoneRequest) }}";
    let csrfToken = "{{ csrf_token() }}";

    // Cambiar entre Zonas / Áreas del restaurante (Mostrando Cuadros de Área separados sin sobreponerse)
    function switchZone(zoneName, btnElem) {
        currentZone = zoneName;
        $('.zone-tab-btn').removeClass('active');
        $(btnElem).addClass('active');

        $('.zone-floor-box').each(function() {
            let z = $(this).attr('data-zone-box');
            if (zoneName === 'TODAS' || z === zoneName) {
                $(this).fadeIn(150);
            } else {
                $(this).hide();
            }
        });
    }

    function openNewTableInZone(zoneName) {
        $('#new_table_zone_select').val(zoneName).trigger('change');
        $('#modalNewTable').modal('show');
    }

    // Cambiar entre Plano 2D Interactivo y Vista Cuadrícula
    function setViewMode(mode) {
        if (mode === 'MAP') {
            $('#mapContainerView').show();
            $('#gridContainerView').hide();
            $('#btnViewMap').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btnViewGrid').removeClass('btn-primary').addClass('btn-outline-primary');
        } else {
            $('#mapContainerView').hide();
            $('#gridContainerView').show();
            $('#btnViewGrid').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btnViewMap').removeClass('btn-primary').addClass('btn-outline-primary');
        }
    }

    // Activar / Desactivar Modo Diseñador (Drag & Drop de Mesas)
    function toggleDesignMode() {
        setViewMode('MAP');
        isDesignMode = !isDesignMode;

        if (isDesignMode) {
            $('.floor-canvas-area').addClass('design-mode');
            $('#designerBanner').slideDown(150);
            $('#btnToggleDesigner').removeClass('btn-outline-dark').addClass('btn-warning text-dark');
            $('#lblDesignMode').text('Guardar Ubicación de Mesas');
        } else {
            $('.floor-canvas-area').removeClass('design-mode');
            $('#designerBanner').slideUp(150);
            $('#btnToggleDesigner').removeClass('btn-warning text-dark').addClass('btn-outline-dark');
            $('#lblDesignMode').text('Diseñar / Mover Mesas');
        }
    }

    // Al hacer clic en una mesa en el plano 2D
    function handleTableClick(tableId) {
        if (isDesignMode) {
            return; // En modo diseño el clic es para arrastrar o editar
        }
        document.getElementById('open_table_form_' + tableId).submit();
    }

    // Mostrar input de nueva área personalizada
    function toggleCustomZoneInput(val, boxId) {
        if (val === '__NEW__') {
            $('#' + boxId).slideDown(150);
            $('#new_custom_zone_input').prop('required', true).focus();
        } else {
            $('#' + boxId).slideUp(150);
            $('#new_custom_zone_input').prop('required', false).val('');
        }
    }

    // Abrir modal de edición de mesa
    function openEditTableModal(id, name, zone, capacity, shape) {
        $('#edit_table_name').val(name);
        $('#edit_table_zone').val(zone);
        $('#edit_table_capacity').val(capacity);
        $('#edit_table_shape').val(shape);
        $('#editTableForm').attr('action', "{{ url('restaurant/tables') }}/" + id);
        $('#deleteTableForm').attr('action', "{{ url('restaurant/tables') }}/" + id);
        $('#modalEditTable').modal('show');
    }

    function deleteSelectedTable() {
        if (confirm('¿Está seguro de eliminar esta mesa del salón?')) {
            document.getElementById('deleteTableForm').submit();
        }
    }

    // =========================================================================
    // MOTOR DRAG & DROP PARA EL PLANO 2D DE MESAS (MOUSE + TOUCH)
    // =========================================================================
    (function initDraggableTables() {
        let activeNode = null;
        let startX = 0, startY = 0;
        let initialLeft = 0, initialTop = 0;
        let hasMoved = false;

        $(document).on('mousedown touchstart', '.map-table-node', function(e) {
            if (!isDesignMode) return;
            if ($(e.target).closest('button').length) return;

            activeNode = this;
            hasMoved = false;

            const touch = e.type === 'touchstart' ? e.originalEvent.touches[0] : e;
            startX = touch.clientX;
            startY = touch.clientY;
            initialLeft = parseInt(activeNode.style.left) || 40;
            initialTop = parseInt(activeNode.style.top) || 40;

            activeNode.style.zIndex = 100;
            e.preventDefault();
        });

        $(document).on('mousemove touchmove', function(e) {
            if (!activeNode || !isDesignMode) return;

            const touch = e.type === 'touchmove' ? e.originalEvent.touches[0] : e;
            let dx = touch.clientX - startX;
            let dy = touch.clientY - startY;

            if (Math.abs(dx) > 3 || Math.abs(dy) > 3) {
                hasMoved = true;
            }

            // Ajuste a cuadrícula suave de 10px para que quede alineado y estético
            let newX = Math.max(10, Math.round((initialLeft + dx) / 10) * 10);
            let newY = Math.max(10, Math.round((initialTop + dy) / 10) * 10);

            activeNode.style.left = newX + 'px';
            activeNode.style.top = newY + 'px';
        });

        $(document).on('mouseup touchend', function() {
            if (!activeNode) return;

            let node = activeNode;
            node.style.zIndex = 5;
            activeNode = null;

            if (hasMoved) {
                let tableId = $(node).attr('data-id');
                let finalX = parseInt(node.style.left) || 40;
                let finalY = parseInt(node.style.top) || 40;

                $.post("{{ url('restaurant/tables') }}/" + tableId + "/position", {
                    _token: csrfToken,
                    pos_x: finalX,
                    pos_y: finalY
                });
            }
        });
    })();
</script>
@endsection
