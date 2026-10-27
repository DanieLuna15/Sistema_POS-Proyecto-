@extends('layouts.admin')
@section('title', 'Salón de Mesas, Comandas y Cocina — Restaurante')

@section('styles')
<style>
    .table-card {
        border-radius: 14px;
        border: 2px solid #e2e8f0;
        transition: all 0.2s ease;
        background: #fff;
        position: relative;
        overflow: hidden;
    }
    .table-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .table-libre { border-color: #10b981; background: #f0fdf4; }
    .table-ocupada { border-color: #ef4444; background: #fef2f2; }
    .table-cuenta { border-color: #f59e0b; background: #fffbeb; }
    .kds-card {
        border-radius: 10px;
        border-left: 5px solid #ef4444;
        background: #fff;
    }
</style>
@endsection

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
    $freeCount = $tables->where('status', 'LIBRE')->count();
    $busyCount = $tables->where('status', 'OCUPADA')->count();
    $billCount = $tables->where('status', 'CUENTA_PEDIDA')->count();
@endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <span class="badge badge-danger mb-1"><i class="fas fa-utensils mr-1"></i> MÓDULO GASTRONÓMICO — {{ $branch->name ?? 'Casa Matriz' }}</span>
            <h3 class="page-title font-weight-bold mb-1">
                <i class="fas fa-concierge-bell text-primary mr-2"></i>Mapa de Mesas, Comandas y Monitor de Cocina
            </h3>
            <small class="text-muted">Haz clic en cualquier mesa libre para abrir comanda, o en una ocupada para agregar platos, imprimir pre-cuenta o cobrar.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button type="button" class="btn btn-warning text-dark font-weight-bold mr-1" data-toggle="modal" data-target="#modalExternalOrder" onclick="setExtType('LLEVAR')">
                <i class="fas fa-shopping-bag mr-1"></i> + Pedido Para Llevar
            </button>
            <button type="button" class="btn btn-info font-weight-bold mr-1" data-toggle="modal" data-target="#modalExternalOrder" onclick="setExtType('DELIVERY')">
                <i class="fas fa-motorcycle mr-1"></i> + Nuevo Delivery
            </button>
            <button type="button" class="btn btn-outline-primary font-weight-bold" data-toggle="modal" data-target="#modalNewTable">
                <i class="fas fa-plus-circle mr-1"></i> Nueva Mesa
            </button>
        </div>
    </div>

    <!-- RESUMEN RÁPIDO DEL SALÓN -->
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-2">
            <div class="p-3 rounded bg-white border d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <small class="text-muted font-weight-bold text-uppercase">Mesas Libres</small>
                    <h3 class="mb-0 font-weight-bold text-success">{{ $freeCount }}</h3>
                </div>
                <i class="fas fa-check-circle fa-2x text-success" style="opacity:0.4;"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="p-3 rounded bg-white border d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <small class="text-muted font-weight-bold text-uppercase">Mesas Ocupadas</small>
                    <h3 class="mb-0 font-weight-bold text-danger">{{ $busyCount }}</h3>
                </div>
                <i class="fas fa-utensils fa-2x text-danger" style="opacity:0.4;"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="p-3 rounded bg-white border d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <small class="text-muted font-weight-bold text-uppercase">Pre-Cuenta Pedida</small>
                    <h3 class="mb-0 font-weight-bold text-warning">{{ $billCount }}</h3>
                </div>
                <i class="fas fa-receipt fa-2x text-warning" style="opacity:0.5;"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="p-3 rounded bg-white border d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <small class="text-muted font-weight-bold text-uppercase">Llevar / Delivery</small>
                    <h3 class="mb-0 font-weight-bold text-info">{{ $externalOrders->count() }}</h3>
                </div>
                <i class="fas fa-motorcycle fa-2x text-info" style="opacity:0.4;"></i>
            </div>
        </div>
    </div>

    <!-- PESTAÑAS: 1. MAPA DE MESAS | 2. PARA LLEVAR & DELIVERY | 3. MONITOR DE COCINA (KDS) -->
    <ul class="nav nav-tabs mb-3 font-weight-bold" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-mesas" role="tab">
                <i class="fas fa-th-large mr-1 text-primary"></i> 1. Salón y Mapa de Mesas ({{ $tables->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-llevar" role="tab">
                <i class="fas fa-motorcycle mr-1 text-info"></i> 2. Para Llevar y Delivery Activos ({{ $externalOrders->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-cocina" role="tab">
                <i class="fas fa-fire-alt mr-1 text-danger"></i> 3. Monitor de Cocina / Comandas en Vivo ({{ $kitchenOrders->count() }})
            </a>
        </li>
    </ul>

    <div class="tab-content border-0 p-0">
        <!-- TAB 1: MAPA VISUAL DE MESAS POR ZONA -->
        <div class="tab-pane fade show active" id="tab-mesas" role="tabpanel">
            @foreach($tables->groupBy('zone') as $zoneName => $zoneTables)
                <h5 class="font-weight-bold text-dark mt-3 mb-2">
                    <i class="fas fa-map-marker-alt text-primary mr-1"></i> Ambiente: {{ $zoneName }}
                </h5>
                <div class="row">
                    @foreach($zoneTables as $t)
                        @php
                            $order = $t->activeOrder;
                            $cardClass = 'table-libre';
                            $badgeClass = 'badge-success';
                            $statusText = 'LIBRE';
                            if ($t->status === 'OCUPADA' && $order) {
                                $cardClass = 'table-ocupada';
                                $badgeClass = 'badge-danger';
                                $statusText = 'OCUPADA';
                            } elseif ($t->status === 'CUENTA_PEDIDA' && $order) {
                                $cardClass = 'table-cuenta';
                                $badgeClass = 'badge-warning text-dark';
                                $statusText = 'PIDIÓ CUENTA';
                            }
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="table-card {{ $cardClass }} p-3 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="font-weight-bold mb-0 text-dark">
                                            <i class="fas fa-chair mr-1"></i> {{ $t->name }}
                                        </h5>
                                        <span class="badge {{ $badgeClass }} px-2 py-1 font-weight-bold">{{ $statusText }}</span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <i class="fas fa-users mr-1"></i> Capacidad: {{ $t->capacity }} personas
                                    </div>

                                    @if($order && !in_array($order->status, ['COBRADA', 'CANCELADA']))
                                        <div class="p-2 rounded bg-white border mb-2 small">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $order->order_number }}</strong>
                                                <span class="text-muted"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($order->opened_at)->format('H:i') }}</span>
                                            </div>
                                            <div class="text-muted">Mesero: {{ $order->user->name ?? 'Caja' }}</div>
                                            <div class="mt-1 font-weight-bold text-primary" style="font-size: 14px;">
                                                Consumo: {{ $sym }} {{ number_format($order->total, 2) }}
                                                <span class="badge badge-light border ml-1">{{ $order->items->sum('quantity') }} ítems</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-3 text-success">
                                            <i class="fas fa-utensils fa-2x mb-1" style="opacity:0.4;"></i>
                                            <div class="small font-weight-bold">Disponible para atender</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-2">
                                    @if($order && !in_array($order->status, ['COBRADA', 'CANCELADA']))
                                        <div class="d-flex" style="gap:6px;">
                                            <a href="{{ route('restaurant.orders.show', $order) }}" class="btn btn-danger btn-sm flex-fill font-weight-bold">
                                                <i class="fas fa-edit mr-1"></i> Ver Comanda / Cobrar
                                            </a>
                                            <a href="{{ route('restaurant.orders.kitchen', $order) }}" target="_blank" class="btn btn-outline-dark btn-sm" title="Imprimir Comanda Cocina 80mm">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    @else
                                        <form action="{{ route('restaurant.tables.open', $t) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm btn-block font-weight-bold">
                                                <i class="fas fa-plus-circle mr-1"></i> Abrir Mesa y Tomar Pedido
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- TAB 2: PEDIDOS PARA LLEVAR Y DELIVERY ACTIVOS -->
        <div class="tab-pane fade" id="tab-llevar" role="tabpanel">
            <div class="row mt-3">
                @forelse($externalOrders as $ext)
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-left-{{ $ext->service_type === 'DELIVERY' ? 'info' : 'warning' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge badge-{{ $ext->service_type === 'DELIVERY' ? 'info' : 'warning text-dark' }} px-2 py-1 font-weight-bold">
                                        <i class="fas {{ $ext->service_type === 'DELIVERY' ? 'fa-motorcycle' : 'fa-shopping-bag' }} mr-1"></i>
                                        {{ $ext->service_type }} — {{ $ext->order_number }}
                                    </span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($ext->opened_at)->format('H:i') }}</small>
                                </div>
                                <h5 class="font-weight-bold mb-1">{{ $ext->customer_name }}</h5>
                                @if($ext->delivery_phone)
                                    <div class="small text-muted"><i class="fas fa-phone mr-1"></i> {{ $ext->delivery_phone }}</div>
                                @endif
                                @if($ext->delivery_address)
                                    <div class="small text-muted"><i class="fas fa-map-marker-alt mr-1"></i> {{ $ext->delivery_address }}</div>
                                @endif
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small">{{ $ext->items->sum('quantity') }} platos/bebidas</span>
                                    <strong class="text-primary h5 mb-0">{{ $sym }} {{ number_format($ext->total, 2) }}</strong>
                                </div>
                                <a href="{{ route('restaurant.orders.show', $ext) }}" class="btn btn-primary btn-sm btn-block font-weight-bold">
                                    <i class="fas fa-cash-register mr-1"></i> Abrir Pedido / Cobrar
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fas fa-motorcycle fa-3x mb-2"></i>
                        <h5>No hay pedidos Para Llevar ni Delivery pendientes en este momento.</h5>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 3: MONITOR DE COCINA / BARRA (KDS EN VIVO) -->
        <div class="tab-pane fade" id="tab-cocina" role="tabpanel">
            <div class="row mt-3">
                @forelse($kitchenOrders as $ko)
                    <div class="col-md-4 mb-3">
                        <div class="kds-card shadow-sm p-3">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <span class="badge badge-dark font-weight-bold">{{ $ko->order_number }}</span>
                                    <strong class="ml-1 text-danger">
                                        {{ $ko->service_type === 'MESA' ? ($ko->table->name ?? 'Mesa') : ($ko->service_type . ': ' . $ko->customer_name) }}
                                    </strong>
                                </div>
                                <small class="text-muted font-weight-bold">
                                    <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($ko->opened_at)->format('H:i') }}
                                </small>
                            </div>

                            @foreach($ko->items as $item)
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <div>
                                        <span class="font-weight-bold">{{ number_format($item->quantity, 0) }}x {{ $item->product->name ?? 'Plato' }}</span>
                                        @if($item->notes)
                                            <br><small class="text-danger font-weight-bold"><i class="fas fa-exclamation-circle"></i> {{ $item->notes }}</small>
                                        @endif
                                    </div>
                                    <div>
                                        @if($item->kitchen_status === 'EN_COCINA')
                                            <form action="{{ route('restaurant.items.status', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="kitchen_status" value="LISTO">
                                                <button type="submit" class="btn btn-xs btn-warning text-dark font-weight-bold" title="Marcar como Listo para Servir">
                                                    🔥 En Cocina &rarr; Listo
                                                </button>
                                            </form>
                                        @elseif($item->kitchen_status === 'LISTO')
                                            <form action="{{ route('restaurant.items.status', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="kitchen_status" value="SERVIDO">
                                                <button type="submit" class="btn btn-xs btn-info font-weight-bold" title="Marcar como Entregado en Mesa">
                                                    🔔 Listo &rarr; Servido
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Servido</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-2 d-flex justify-content-between">
                                <a href="{{ route('restaurant.orders.kitchen', $ko) }}" target="_blank" class="btn btn-outline-dark btn-xs">
                                    <i class="fas fa-print"></i> Imprimir Comanda
                                </a>
                                <a href="{{ route('restaurant.orders.show', $ko) }}" class="btn btn-outline-primary btn-xs">
                                    Ir a la Cuenta &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                        <h5>No hay comandas pendientes en cocina.</h5>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Nueva Mesa -->
<div class="modal fade" id="modalNewTable" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('restaurant.tables.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-chair mr-2"></i>Agregar Nueva Mesa al Salón</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Nombre / Número de Mesa *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ej: Mesa 7 / VIP 1 / Terraza 2" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Zona / Ambiente *</label>
                    <input type="text" name="zone" class="form-control" value="Salón Principal" placeholder="Ej: Salón Principal, Terraza, Segundo Piso, Barra" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Capacidad (Comensales) *</label>
                    <input type="number" name="capacity" class="form-control" value="4" min="1" max="30" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Guardar Mesa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pedido Para Llevar / Delivery -->
<div class="modal fade" id="modalExternalOrder" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('restaurant.orders.external') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-motorcycle mr-2"></i>Nueva Comanda Para Llevar / Delivery</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Modalidad de Servicio *</label>
                    <select name="service_type" id="ext_service_type" class="form-control font-weight-bold" onchange="toggleDeliveryFields()">
                        <option value="LLEVAR">🛍️ PARA LLEVAR (Recoge en Mostrador)</option>
                        <option value="DELIVERY">🛵 DELIVERY (Envío a Domicilio)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Nombre del Cliente *</label>
                    <input type="text" name="customer_name" class="form-control" placeholder="Ej: Familia Gómez / Juan Pérez" required>
                </div>
                <div id="deliveryFieldsBox" style="display:none;">
                    <div class="form-group">
                        <label class="font-weight-bold">Teléfono / WhatsApp</label>
                        <input type="text" name="delivery_phone" class="form-control" placeholder="Ej: 70012345">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Dirección de Entrega / Referencia</label>
                        <input type="text" name="delivery_address" class="form-control" placeholder="Ej: Av. América #450, Edif. Sol Piso 3">
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Nota General del Pedido</label>
                    <input type="text" name="notes" class="form-control" placeholder="Ej: Incluir cubiertos y servilletas...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-info font-weight-bold">Continuar a Seleccionar Platos &rarr;</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setExtType(type) {
        document.getElementById('ext_service_type').value = type;
        toggleDeliveryFields();
    }
    function toggleDeliveryFields() {
        var t = document.getElementById('ext_service_type').value;
        document.getElementById('deliveryFieldsBox').style.display = (t === 'DELIVERY') ? 'block' : 'none';
    }
</script>
@endsection
