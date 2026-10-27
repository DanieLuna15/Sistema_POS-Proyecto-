@extends('layouts.admin')
@section('title', 'Kardex de Inventario y Ajustes de Stock')

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-clipboard-list text-warning mr-2"></i> Kardex Auditable y Ajustes de Inventario
            </h3>
            <small class="text-muted">Trazabilidad completa de entradas, salidas, anulaciones, mermas, vencimientos y conteo físico.</small>
        </div>
        <div>
            <a href="{{ route('transfers.index') }}" class="btn btn-info font-weight-bold mr-1">
                <i class="fas fa-truck-loading mr-1"></i> Traspasos entre Sucursales
            </a>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#adjustModal">
                <i class="fas fa-Sliders-h fa-plus-circle mr-1"></i> Registrar Ajuste / Merma
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ml-1">
                <i class="fas fa-boxes"></i> Ver Catálogo
            </a>
        </div>
    </div>

    <!-- ALERTAS DE STOCK MÍNIMO Y PRÓXIMOS VENCIMIENTOS -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card border-left-warning shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0 text-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Alertas de Stock Bajo / Agotado ({{ $lowStockProducts->count() }})
                        </h5>
                        <a href="{{ route('purchases.create') }}" class="btn btn-warning btn-sm text-dark font-weight-bold">Abastecer</a>
                    </div>
                    @if($lowStockProducts->count() > 0)
                        <div style="max-height: 130px; overflow-y: auto;">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Ubicación</th>
                                        <th>Mínimo</th>
                                        <th>Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $lp)
                                    <tr>
                                        <td><a href="{{ route('products.show', $lp) }}">{{ $lp->name }}</a></td>
                                        <td>{{ $lp->location ?? '-' }}</td>
                                        <td>{{ $lp->stock_min }} {{ $lp->unit_measure }}</td>
                                        <td>
                                            <span class="badge {{ $lp->stock <= 0 ? 'badge-danger' : 'badge-warning' }}">
                                                {{ $lp->stock }} {{ $lp->unit_measure }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-success mb-0"><i class="fas fa-check-circle"></i> Todos los productos superan su stock mínimo configurado.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-left-danger shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0 text-danger">
                            <i class="fas fa-calendar-times mr-1"></i> Control de Lotes y Vencimientos Próximos ({{ $expiringProducts->count() }})
                        </h5>
                    </div>
                    @if($expiringProducts->count() > 0)
                        <div style="max-height: 130px; overflow-y: auto;">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Lote</th>
                                        <th>Vencimiento</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringProducts as $ep)
                                    @php
                                        $isExpired = \Carbon\Carbon::parse($ep->expiration_date)->isPast();
                                    @endphp
                                    <tr>
                                        <td><a href="{{ route('products.show', $ep) }}">{{ $ep->name }}</a></td>
                                        <td>{{ $ep->batch_number ?? 'S/L' }}</td>
                                        <td>
                                            <span class="badge {{ $isExpired ? 'badge-danger' : 'badge-warning' }}">
                                                {{ \Carbon\Carbon::parse($ep->expiration_date)->format('d/m/Y') }}
                                                {{ $isExpired ? '(VENCIDO)' : '' }}
                                            </span>
                                        </td>
                                        <td>{{ $ep->stock }} {{ $ep->unit_measure }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0"><i class="fas fa-check-circle text-success"></i> No hay lotes vencidos ni próximos a vencer en los siguientes 45 días.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- FILTROS E HISTORIAL DE KARDEX -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('kardex.index') }}" class="row align-items-end mb-4">
                <div class="col-md-3">
                    <label class="font-weight-bold">Sucursal</label>
                    <select name="branch_id" class="form-control">
                        <option value="">Sucursal Activa ({{ $activeBranch->name ?? 'Actual' }})</option>
                        <option value="ALL" {{ request('branch_id') === 'ALL' ? 'selected' : '' }}>-- Todas las Sucursales --</option>
                        @if(isset($branches))
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="font-weight-bold">Filtrar por Producto</label>
                    <select name="product_id" class="form-control">
                        <option value="">-- Todos los productos --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                                [{{ $p->code }}] {{ $p->name }} (Stock: {{ $p->stock }} {{ $p->unit_measure }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold">Tipo de Movimiento</label>
                    <select name="movement_type" class="form-control">
                        <option value="">-- Todos los movimientos --</option>
                        <option value="ENTRADA_COMPRA" {{ request('movement_type') == 'ENTRADA_COMPRA' ? 'selected' : '' }}>Entrada por Compra</option>
                        <option value="SALIDA_VENTA" {{ request('movement_type') == 'SALIDA_VENTA' ? 'selected' : '' }}>Salida por Venta</option>
                        <option value="TRASPASO_ENTRADA" {{ request('movement_type') == 'TRASPASO_ENTRADA' ? 'selected' : '' }}>Entrada por Traspaso</option>
                        <option value="TRASPASO_SALIDA" {{ request('movement_type') == 'TRASPASO_SALIDA' ? 'selected' : '' }}>Salida por Traspaso</option>
                        <option value="AJUSTE_ENTRADA" {{ request('movement_type') == 'AJUSTE_ENTRADA' ? 'selected' : '' }}>Ajuste Manual (Entrada)</option>
                        <option value="AJUSTE_SALIDA" {{ request('movement_type') == 'AJUSTE_SALIDA' ? 'selected' : '' }}>Ajuste Manual (Salida)</option>
                        <option value="MERMA_VENCIMIENTO" {{ request('movement_type') == 'MERMA_VENCIMIENTO' ? 'selected' : '' }}>Baja por Merma / Vencimiento</option>
                        <option value="INVENTARIO_INICIAL" {{ request('movement_type') == 'INVENTARIO_INICIAL' ? 'selected' : '' }}>Inventario Inicial</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info mr-1"><i class="fas fa-filter"></i> Filtrar</button>
                    <a href="{{ route('kardex.index') }}" class="btn btn-light">Limpiar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="order-listing" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha / Hora</th>
                            <th>Referencia</th>
                            <th>Producto</th>
                            <th>Tipo Movimiento</th>
                            <th class="text-center">Stock Ant.</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Saldo Stock</th>
                            <th>Usuario / Sucursal</th>
                            <th>Detalle / Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $mov)
                        @php $isEntry = $mov->isEntry(); @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov->movement_date)->format('d/m/Y H:i') }}</td>
                            <td><span class="badge badge-outline-dark">{{ $mov->reference_code ?? 'MOV-'.$mov->id }}</span></td>
                            <td>
                                @if($mov->product)
                                    <strong>{{ $mov->product->name }}</strong><br>
                                    <small class="text-muted">Cód: {{ $mov->product->code }}</small>
                                @else
                                    <span class="text-muted">Producto eliminado</span>
                                @endif
                            </td>
                            <td>
                                @if($isEntry)
                                    <span class="badge badge-success"><i class="fas fa-arrow-up mr-1"></i> {{ $mov->movement_type }}</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-arrow-down mr-1"></i> {{ $mov->movement_type }}</span>
                                @endif
                            </td>
                            <td class="text-center text-muted">{{ number_format($mov->previous_stock, 0) }}</td>
                            <td class="text-center font-weight-bold {{ $isEntry ? 'text-success' : 'text-danger' }}">
                                {{ $isEntry ? '+' : '-' }}{{ number_format($mov->quantity, 0) }}
                            </td>
                            <td class="text-center font-weight-bold text-primary">{{ number_format($mov->new_stock, 0) }}</td>
                            <td>
                                {{ $mov->user->name ?? 'Sistema' }}<br>
                                <small class="text-muted">{{ $mov->branch->name ?? 'Central' }}</small>
                            </td>
                            <td>{{ $mov->notes }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajuste Manual de Inventario / Merma -->
<div class="modal fade" id="adjustModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('kardex.adjust') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-clipboard-check mr-2"></i> Registrar Ajuste de Stock o Merma</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Producto Físico *</label>
                        <select name="product_id" class="form-control" required>
                            <option value="">-- Seleccione un producto --</option>
                            @foreach($products->where('item_type', '!=', 'SERVICIO') as $p)
                                <option value="{{ $p->id }}">[{{ $p->code }}] {{ $p->name }} (Stock actual: {{ $p->stock }} {{ $p->unit_measure }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-7 form-group">
                            <label class="font-weight-bold">Tipo de Ajuste *</label>
                            <select name="movement_type" class="form-control" required>
                                <option value="AJUSTE_ENTRADA"> (+) Entrada por Conteo / Sobrante</option>
                                <option value="INVENTARIO_INICIAL"> (+) Carga de Inventario Inicial</option>
                                <option value="AJUSTE_SALIDA"> (-) Salida por Ajuste / Uso Interno</option>
                                <option value="MERMA_VENCIMIENTO"> (-) Baja por Merma / Daño / Vencimiento</option>
                            </select>
                        </div>
                        <div class="col-md-5 form-group">
                            <label class="font-weight-bold">Cantidad *</label>
                            <input type="number" step="1" min="1" name="quantity" class="form-control" placeholder="Ej: 5" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Motivo / Observación Auditable *</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Explique el motivo del ajuste (Ej: Conteo físico de almacén, producto dañado, vencimiento de lote...)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Aplicar Ajuste al Kardex</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
@endsection

