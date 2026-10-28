@extends('layouts.admin')
@section('title', 'Pantalla de Cocina (KDS) — ' . ($activeCompany->name ?? 'Restaurante'))

@section('styles')
<style>
    .kds-header-bar {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
        border-radius: 16px;
        padding: 18px 22px;
        margin-bottom: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.16);
    }
    .kds-kpi-box {
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 12px;
        padding: 8px 16px;
        text-align: center;
        min-width: 120px;
    }
    .kds-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 18px;
    }
    .kds-ticket-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        border: 2px solid #cbd5e1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    .kds-ticket-card.urgency-normal {
        border-top: 6px solid #10b981;
    }
    .kds-ticket-card.urgency-warning {
        border-top: 6px solid #f59e0b;
    }
    .kds-ticket-card.urgency-critical {
        border-top: 6px solid #ef4444;
    }
    .kds-ticket-card.order-all-ready {
        border-color: #3b82f6;
        border-top: 6px solid #2563eb;
    }
    .kds-ticket-head {
        padding: 12px 14px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .kds-dish-row {
        padding: 11px 14px;
        border-bottom: 1px dashed #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        transition: background 0.2s ease;
    }
    .kds-dish-row.status-EN_COCINA {
        background: #fffbeb;
    }
    .kds-dish-row.status-LISTO {
        background: #eff6ff;
    }
    .kds-dish-row.status-SERVIDO {
        background: #f8fafc;
    }
    .kds-qty-circle {
        min-width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #1e293b;
        color: #fff;
        font-weight: 900;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kds-dish-thumb {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        object-fit: cover;
        background: #fff;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .kds-note-alert {
        background: #fee2e2;
        color: #991b1b;
        border-left: 3px solid #ef4444;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11.5px;
        font-weight: 800;
        margin-top: 3px;
        display: inline-block;
    }
    .btn-kds-status {
        min-width: 115px;
        font-weight: 800;
        font-size: 12px;
        border-radius: 8px;
        padding: 7px 10px;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-kds-cooking {
        background: #10b981;
        color: #fff;
        box-shadow: 0 3px 8px rgba(16, 185, 129, 0.3);
    }
    .btn-kds-cooking:hover {
        background: #059669;
        color: #fff;
    }
    .btn-kds-ready {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 3px 8px rgba(37, 99, 235, 0.3);
    }
    .btn-kds-ready:hover {
        background: #1d4ed8;
        color: #fff;
    }
    .btn-kds-served {
        background: #e2e8f0;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
    .btn-kds-served:hover {
        background: #cbd5e1;
        color: #0f172a;
    }
</style>
@endsection

@section('content')
@php
    $totalPendingDishes = 0;
    $totalReadyDishes = 0;
    foreach ($kitchenOrders as $ko) {
        $totalPendingDishes += $ko->items->where('kitchen_status', 'EN_COCINA')->sum('quantity');
        $totalReadyDishes += $ko->items->where('kitchen_status', 'LISTO')->sum('quantity');
    }
@endphp

<div class="content-wrapper">
    <!-- BARRA DE CONTROL SUPERIOR DE COCINA (KDS) -->
    <div class="kds-header-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
            <div>
                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1 mb-1">
                    <i class="fas fa-fire-alt text-danger mr-1"></i> MÓDULO EXCLUSIVO DE COCINA Y DESPACHO (KDS — INSTANTÁNEO SIN RECARGAR)
                </span>
                <h3 class="font-weight-bold mb-0 text-white">
                    Monitor de Preparación en Vivo — {{ $branch->name ?? 'Sucursal Principal' }}
                </h3>
                <small class="text-light opacity-75">
                    Toque el botón de cada plato para cambiar su estado al instante (En Cocina &rarr; ¡Listo! &rarr; Servido) sin recargar la pantalla.
                </small>
            </div>

            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                <div class="kds-kpi-box">
                    <small class="d-block text-warning font-weight-bold">EN FUEGO</small>
                    <span class="h3 font-weight-bold mb-0 text-white" id="kpiCookingCount">{{ number_format($totalPendingDishes, 0) }}</span>
                </div>
                <div class="kds-kpi-box">
                    <small class="d-block text-info font-weight-bold">LISTOS P/ MESERO</small>
                    <span class="h3 font-weight-bold mb-0 text-white" id="kpiReadyCount">{{ number_format($totalReadyDishes, 0) }}</span>
                </div>
                <div>
                    <a href="{{ route('restaurant.kitchen') }}" class="btn btn-light font-weight-bold mr-1">
                        <i class="fas fa-sync-alt mr-1"></i> Buscar Nuevos Pedidos
                    </a>
                    <a href="{{ route('restaurant.index') }}" class="btn btn-warning text-dark font-weight-bold">
                        <i class="fas fa-map-marked-alt mr-1"></i> Mapa de Mesas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTROS RÁPIDOS -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 10px;">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-dark font-weight-bold kds-filter-btn" onclick="filterKds('ALL', this)">
                <i class="fas fa-th-large mr-1"></i> Todas las Comandas ({{ $kitchenOrders->count() }})
            </button>
            <button type="button" class="btn btn-outline-dark font-weight-bold kds-filter-btn" onclick="filterKds('COOKING', this)">
                🔥 En Preparación
            </button>
            <button type="button" class="btn btn-outline-dark font-weight-bold kds-filter-btn" onclick="filterKds('READY', this)">
                🔔 Listas / Despachadas
            </button>
        </div>

        <div class="small text-muted font-weight-bold">
            <span class="badge badge-success mr-1">&lt; 10 min</span> A tiempo
            <span class="badge badge-warning text-dark ml-2 mr-1">10 - 20 min</span> Atención
            <span class="badge badge-danger ml-2 mr-1">&gt; 20 min</span> Prioridad
        </div>
    </div>

    <!-- TARJETAS DE COMANDAS EN COCINA -->
    @if($kitchenOrders->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4 class="font-weight-bold text-dark">¡Todo al día en Cocina!</h4>
                <p class="text-muted mb-0">No hay comandas pendientes de preparación en este momento.</p>
            </div>
        </div>
    @else
        <div class="kds-grid">
            @foreach($kitchenOrders as $kOrder)
                @php
                    $openedCarbon = $kOrder->opened_at ? \Carbon\Carbon::parse($kOrder->opened_at) : null;
                    $minutesElapsed = $openedCarbon ? $openedCarbon->diffInMinutes(now()) : 0;
                    $urgencyClass = $minutesElapsed >= 20 ? 'urgency-critical' : ($minutesElapsed >= 10 ? 'urgency-warning' : 'urgency-normal');
                    $pendingInOrder = $kOrder->items->where('kitchen_status', 'EN_COCINA')->count();
                    $allReady = ($pendingInOrder === 0 && $kOrder->items->isNotEmpty());
                @endphp

                <div class="kds-ticket-card {{ $urgencyClass }} {{ $allReady ? 'order-all-ready' : '' }}"
                     id="kds_card_{{ $kOrder->id }}"
                     data-order-id="{{ $kOrder->id }}"
                     data-state="{{ $pendingInOrder > 0 ? 'COOKING' : 'READY' }}">
                    <div>
                        <!-- CABECERA DE LA COMANDA (SIN CORTES NI DESBORDES) -->
                        <div class="kds-ticket-head">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 6px;">
                                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                    <span class="badge badge-dark font-weight-bold px-2 py-1" style="font-size: 12.5px;">
                                        {{ $kOrder->order_number }}
                                    </span>
                                    @if($kOrder->service_type === 'MESA')
                                        <span class="badge badge-danger font-weight-bold px-2 py-1" style="font-size: 12.5px;">
                                            <i class="fas fa-chair mr-1"></i> {{ $kOrder->table->name ?? 'Mesa' }} ({{ $kOrder->table->zone ?? 'Salón' }})
                                        </span>
                                    @else
                                        <span class="badge badge-info font-weight-bold px-2 py-1" style="font-size: 12.5px;">
                                            <i class="fas fa-shopping-bag mr-1"></i> {{ $kOrder->service_type }}: {{ $kOrder->customer_name }}
                                        </span>
                                    @endif
                                </div>

                                <span class="badge {{ $minutesElapsed >= 20 ? 'badge-danger' : ($minutesElapsed >= 10 ? 'badge-warning text-dark' : 'badge-success') }} px-2 py-1 font-weight-bold">
                                    <i class="far fa-clock mr-1"></i> {{ $minutesElapsed }} min
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted font-weight-bold">
                                    <i class="fas fa-user-tie mr-1"></i> Mesero: {{ $kOrder->user->name ?? 'Caja' }}
                                    &bull; Hora: {{ $openedCarbon ? $openedCarbon->format('H:i') : '--:--' }}
                                </small>

                                <span id="order_ready_badge_{{ $kOrder->id }}"
                                      class="badge badge-primary px-2 py-1 font-weight-bold"
                                      style="{{ $allReady ? '' : 'display:none;' }}">
                                    🔔 ORDEN LISTA
                                </span>
                            </div>
                        </div>

                        <!-- LISTA DE PLATOS A PREPARAR -->
                        <div>
                            @foreach($kOrder->items as $item)
                                @php
                                    $prod = $item->product;
                                    $hasImg = $prod && $prod->image && file_exists(public_path('image/' . $prod->image));
                                @endphp
                                <div class="kds-dish-row status-{{ $item->kitchen_status }}"
                                     id="kds_item_row_{{ $item->id }}"
                                     data-item-id="{{ $item->id }}"
                                     data-order-id="{{ $kOrder->id }}"
                                     data-qty="{{ intval($item->quantity) }}"
                                     data-status="{{ $item->kitchen_status }}">

                                    <div class="d-flex align-items-center" style="flex: 1; min-width: 0; gap: 10px;">
                                        <div class="kds-qty-circle">
                                            {{ number_format($item->quantity, 0) }}x
                                        </div>

                                        <div class="kds-dish-thumb">
                                            @if($hasImg)
                                                <img src="{{ asset('image/' . $prod->image) }}" class="w-100 h-100 rounded" style="object-fit:cover;">
                                            @else
                                                <i class="fas fa-utensils text-secondary"></i>
                                            @endif
                                        </div>

                                        <div style="min-width: 0; flex: 1;">
                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 14px;" title="{{ $prod->name ?? 'Plato' }}">
                                                {{ $prod->name ?? 'Plato' }}
                                            </div>
                                            @if($item->notes)
                                                <div class="kds-note-alert">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ strtoupper($item->notes) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- BOTÓN INTERACTIVO AJAX (CERO RECARGA DE PÁGINA) -->
                                    <div>
                                        @if($item->kitchen_status === 'EN_COCINA')
                                            <button type="button"
                                                    class="btn-kds-status btn-kds-cooking"
                                                    id="kds_btn_{{ $item->id }}"
                                                    onclick="cycleDishStatusAjax({{ $item->id }}, {{ $kOrder->id }})">
                                                <i class="fas fa-check mr-1"></i> ¡LISTO!
                                            </button>
                                        @elseif($item->kitchen_status === 'LISTO')
                                            <button type="button"
                                                    class="btn-kds-status btn-kds-ready"
                                                    id="kds_btn_{{ $item->id }}"
                                                    onclick="cycleDishStatusAjax({{ $item->id }}, {{ $kOrder->id }})">
                                                🔔 Entregar
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn-kds-status btn-kds-served"
                                                    id="kds_btn_{{ $item->id }}"
                                                    onclick="cycleDishStatusAjax({{ $item->id }}, {{ $kOrder->id }})"
                                                    title="Toque si desea regresar a preparación">
                                                ✔️ Servido
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- PIE DE LA TARJETA: ACCIONES RÁPIDAS AJAX -->
                    <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center" style="gap: 8px;">
                        <a href="{{ route('restaurant.orders.kitchen', $kOrder) }}" target="_blank" class="btn btn-outline-dark btn-sm font-weight-bold">
                            <i class="fas fa-print mr-1"></i> Imprimir 80mm
                        </a>

                        <button type="button"
                                id="btn_all_ready_{{ $kOrder->id }}"
                                class="btn btn-success btn-sm font-weight-bold flex-grow-1 py-2"
                                style="{{ $pendingInOrder > 0 ? '' : 'display:none;' }}"
                                onclick="markEntireOrderAjax({{ $kOrder->id }}, 'LISTO')">
                            <i class="fas fa-check-double mr-1"></i> ¡TODA LA COMANDA LISTA!
                        </button>

                        <a href="{{ route('restaurant.orders.show', $kOrder) }}"
                           id="btn_view_order_{{ $kOrder->id }}"
                           class="btn btn-primary btn-sm font-weight-bold"
                           style="{{ $pendingInOrder === 0 ? '' : 'display:none;' }}">
                            <i class="fas fa-receipt mr-1"></i> Ver Cuenta / Mesa
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    var csrfToken = "{{ csrf_token() }}";

    // Cambiar el estado de un plato en Cocina por AJAX sin recargar la página
    function cycleDishStatusAjax(itemId, orderId) {
        var row = document.getElementById('kds_item_row_' + itemId);
        var btn = document.getElementById('kds_btn_' + itemId);
        if (!row || !btn) return;

        var currentStatus = row.getAttribute('data-status') || 'EN_COCINA';
        var nextStatus = 'LISTO';
        if (currentStatus === 'EN_COCINA') {
            nextStatus = 'LISTO';
        } else if (currentStatus === 'LISTO') {
            nextStatus = 'SERVIDO';
        } else {
            nextStatus = 'EN_COCINA'; // Permite revertir si se tocó por error
        }

        // Actualizar interfaz instantáneamente (Optimistic UI)
        applyRowStatusVisual(row, btn, nextStatus);
        recalculateOrderAndKpiState(orderId);

        // Guardar en segundo plano vía AJAX
        $.ajax({
            url: "{{ url('restaurant/items') }}/" + itemId + "/status",
            method: "POST",
            dataType: "json",
            data: {
                _token: csrfToken,
                kitchen_status: nextStatus
            }
        });
    }

    // Marcar toda una comanda como LISTA con 1 clic vía AJAX sin recargar la página
    function markEntireOrderAjax(orderId, targetStatus) {
        var rows = document.querySelectorAll('.kds-dish-row[data-order-id="' + orderId + '"]');
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            var st = r.getAttribute('data-status');
            if (st !== 'SERVIDO') {
                var itemId = r.getAttribute('data-item-id');
                var btn = document.getElementById('kds_btn_' + itemId);
                applyRowStatusVisual(r, btn, targetStatus);
            }
        }
        recalculateOrderAndKpiState(orderId);

        $.ajax({
            url: "{{ url('restaurant/orders') }}/" + orderId + "/ready",
            method: "POST",
            dataType: "json",
            data: {
                _token: csrfToken,
                target_status: targetStatus
            }
        });
    }

    function applyRowStatusVisual(row, btn, status) {
        row.setAttribute('data-status', status);
        row.classList.remove('status-EN_COCINA', 'status-LISTO', 'status-SERVIDO');
        row.classList.add('status-' + status);

        btn.classList.remove('btn-kds-cooking', 'btn-kds-ready', 'btn-kds-served');
        if (status === 'EN_COCINA') {
            btn.classList.add('btn-kds-cooking');
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> ¡LISTO!';
        } else if (status === 'LISTO') {
            btn.classList.add('btn-kds-ready');
            btn.innerHTML = '🔔 Entregar';
        } else {
            btn.classList.add('btn-kds-served');
            btn.innerHTML = '✔️ Servido';
        }
    }

    function recalculateOrderAndKpiState(orderId) {
        // 1. Verificar estado de la tarjeta de la comanda
        var orderRows = document.querySelectorAll('.kds-dish-row[data-order-id="' + orderId + '"]');
        var pendingInOrder = 0;
        for (var i = 0; i < orderRows.length; i++) {
            if (orderRows[i].getAttribute('data-status') === 'EN_COCINA') {
                pendingInOrder++;
            }
        }

        var card = document.getElementById('kds_card_' + orderId);
        var readyBadge = document.getElementById('order_ready_badge_' + orderId);
        var btnAllReady = document.getElementById('btn_all_ready_' + orderId);
        var btnViewOrder = document.getElementById('btn_view_order_' + orderId);

        if (pendingInOrder === 0) {
            if (card) {
                card.classList.add('order-all-ready');
                card.setAttribute('data-state', 'READY');
            }
            if (readyBadge) readyBadge.style.display = 'inline-block';
            if (btnAllReady) btnAllReady.style.display = 'none';
            if (btnViewOrder) btnViewOrder.style.display = 'inline-block';
        } else {
            if (card) {
                card.classList.remove('order-all-ready');
                card.setAttribute('data-state', 'COOKING');
            }
            if (readyBadge) readyBadge.style.display = 'none';
            if (btnAllReady) btnAllReady.style.display = 'inline-block';
            if (btnViewOrder) btnViewOrder.style.display = 'none';
        }

        // 2. Actualizar contadores superiores en vivo
        var allRows = document.querySelectorAll('.kds-dish-row');
        var cookingTotal = 0;
        var readyTotal = 0;
        for (var j = 0; j < allRows.length; j++) {
            var q = parseInt(allRows[j].getAttribute('data-qty')) || 1;
            var st = allRows[j].getAttribute('data-status');
            if (st === 'EN_COCINA') cookingTotal += q;
            if (st === 'LISTO') readyTotal += q;
        }
        document.getElementById('kpiCookingCount').innerText = cookingTotal;
        document.getElementById('kpiReadyCount').innerText = readyTotal;
    }

    // Filtro en vivo por estado de preparación
    function filterKds(mode, btnElem) {
        $('.kds-filter-btn').removeClass('btn-dark').addClass('btn-outline-dark');
        $(btnElem).removeClass('btn-outline-dark').addClass('btn-dark');

        $('.kds-ticket-card').each(function() {
            var st = $(this).attr('data-state');
            if (mode === 'ALL' || st === mode) {
                $(this).fadeIn(150);
            } else {
                $(this).hide();
            }
        });
    }
</script>
@endsection
