@extends('layouts.admin')
@section('title', 'Catálogo de Productos y Servicios')

@section('styles')
<style>
    /* Pestañas de Navegación Modernas */
    .catalog-filter-tab {
        border-radius: 20px;
        padding: 7px 18px;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none !important;
    }
    .catalog-filter-tab:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .catalog-filter-tab.active-all {
        background: #4f46e5;
        color: #ffffff;
        border-color: #4f46e5;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
    }
    .catalog-filter-tab.active-combo {
        background: #f59e0b;
        color: #78350f;
        border-color: #f59e0b;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25);
    }
    .catalog-filter-tab.active-service {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
    }
    .catalog-filter-tab.active-product {
        background: #10b981;
        color: #ffffff;
        border-color: #10b981;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
    }

    /* Tarjetas Especiales de Combos (Limpio, Gastronómico, sin sobrecarga visual) */
    .combo-showcase-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    }
    .combo-showcase-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.10);
        border-color: #f59e0b;
    }
    .combo-img-header {
        position: relative;
        height: 140px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid #f1f5f9;
        padding: 8px;
    }
    .combo-img-header img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        transition: transform 0.25s ease;
    }
    .combo-showcase-card:hover .combo-img-header img {
        transform: scale(1.06);
    }
    .combo-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 12px;
        color: #1e293b;
        margin-bottom: 6px;
        width: 100%;
    }
    .combo-chip-kardex {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }
    .combo-chip-kitchen {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1e40af;
    }
    .combo-finance-box {
        background: #fefce8;
        border: 1px solid #fef08a;
        border-radius: 12px;
        padding: 10px 14px;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
    $rubro = $activeCompany->rubro_config;
@endphp
<div class="content-wrapper">
    <!-- CABECERA PRINCIPAL -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div>
            <span class="badge badge-primary mb-1"><i class="{{ $rubro['icon'] }} mr-1"></i> Rubro: {{ $rubro['label'] }}</span>
            <h3 class="page-title font-weight-bold mb-1 text-dark">
                {{ $rubro['product_title'] }}
            </h3>
            <small class="text-muted">{{ $rubro['desc'] }}</small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap" style="gap: 8px;">
            <a href="{{route('transfers.index')}}" class="btn btn-outline-primary font-weight-bold">
                <i class="fas fa-truck-loading mr-1"></i> Traspasos
            </a>
            @if($activeCompany->enable_kardex ?? true)
            <a href="{{route('kardex.index')}}" class="btn btn-outline-warning text-dark font-weight-bold">
                <i class="fas fa-clipboard-list mr-1"></i> Kardex
            </a>
            @endif
            <a href="{{route('products.create', ['type' => 'COMBO'])}}" class="btn btn-warning text-dark font-weight-bold shadow-sm">
                <i class="fas fa-gifts mr-1 text-danger"></i> + Nuevo Combo / Promoción
            </a>
            <a href="{{route('products.create')}}" class="btn btn-primary font-weight-bold shadow-sm">
                <i class="fas fa-plus mr-1"></i> Registrar en {{ $rubro['short_label'] }}
            </a>
        </div>
    </div>

    <!-- PESTAÑAS DE FILTRO POR TIPO (ESTILO PÍLDORAS MODERNAS) -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 10px;">
        <div class="d-flex flex-wrap" style="gap: 8px;">
            <a href="{{ route('products.index') }}" class="catalog-filter-tab {{ empty($typeFilter) ? 'active-all' : '' }}">
                <i class="fas fa-th-large"></i> Todos los Ítems
            </a>
            <a href="{{ route('products.index', ['type' => 'COMBO']) }}" class="catalog-filter-tab {{ ($typeFilter ?? '') === 'COMBO' ? 'active-combo' : '' }}">
                <i class="fas fa-gifts text-warning"></i> Combos y Promociones
            </a>
            <a href="{{ route('products.index', ['type' => 'SERVICIO']) }}" class="catalog-filter-tab {{ ($typeFilter ?? '') === 'SERVICIO' ? 'active-service' : '' }}">
                <i class="fas fa-utensils text-info"></i> Platos de Cocina
            </a>
            <a href="{{ route('products.index', ['type' => 'PRODUCTO']) }}" class="catalog-filter-tab {{ ($typeFilter ?? '') === 'PRODUCTO' ? 'active-product' : '' }}">
                <i class="fas fa-box-open text-success"></i> Bebidas & Insumos (Kardex)
            </a>
        </div>

        @if(($typeFilter ?? '') === 'COMBO')
        <!-- SELECTOR DE VISTA EN MODO COMBOS -->
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" id="btnViewCards" class="btn btn-primary font-weight-bold" onclick="toggleComboView('cards')">
                <i class="fas fa-th-large mr-1"></i> Tarjetas Visuales
            </button>
            <button type="button" id="btnViewTable" class="btn btn-outline-primary font-weight-bold" onclick="toggleComboView('table')">
                <i class="fas fa-table mr-1"></i> Tabla Compacta
            </button>
        </div>
        @endif
    </div>

    @if(($typeFilter ?? '') === 'COMBO')
    <!-- ========================================================================= -->
    <!-- VISTA 1: TARJETAS PROMOCIONALES MODERNAS (CERO CARGA VISUAL, MUY ESTÉTICO)-->
    <!-- ========================================================================= -->
    <div id="combosCardView" class="row">
        @forelse($products as $combo)
            @php
                $totalComponentsNormal = 0;
                foreach($combo->comboItems as $ci) {
                    if ($ci->componentProduct) {
                        $totalComponentsNormal += floatval($ci->componentProduct->sell_price) * floatval($ci->quantity);
                    }
                }
                $saving = max(0, $totalComponentsNormal - floatval($combo->sell_price));
                $profit = floatval($combo->sell_price) - floatval($combo->purchase_price);
                $marginPct = ($combo->purchase_price > 0) ? round(($profit / $combo->purchase_price) * 100, 1) : 0;
            @endphp
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="combo-showcase-card">
                    <!-- CABECERA VISUAL CON IMAGEN LIMPIA Y BADGES FLOTANTES -->
                    <div class="combo-img-header">
                        <span class="badge badge-dark font-weight-bold" style="position: absolute; top: 12px; left: 14px; z-index: 2; font-size: 11px; padding: 4px 8px; border-radius: 6px; letter-spacing: 0.5px;">
                            <i class="fas fa-barcode mr-1"></i> {{ $combo->code ?? 'CMB' }}
                        </span>

                        <a href="{{ route('change.status.products', $combo) }}"
                           class="badge {{ $combo->status == 'ACTIVO' ? 'badge-success' : 'badge-danger' }} font-weight-bold"
                           style="position: absolute; top: 12px; right: 14px; z-index: 2; font-size: 11px; padding: 4px 8px; border-radius: 6px; text-decoration: none;"
                           title="Clic para pausar o activar combo">
                            {{ $combo->status == 'ACTIVO' ? '✓ ACTIVO' : '✕ INACTIVO' }}
                        </a>

                        @if($combo->image && file_exists(public_path('image/' . $combo->image)))
                            <img src="{{ asset('image/' . $combo->image) }}" alt="{{ $combo->name }}">
                        @else
                            <i class="fas fa-gifts text-warning fa-4x opacity-50"></i>
                        @endif
                    </div>

                    <!-- CUERPO DE LA TARJETA -->
                    <div class="p-3 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h5 class="font-weight-bold text-dark mb-0" style="font-size: 16px; line-height: 1.3;">
                                    {{ $combo->name }}
                                </h5>
                            </div>
                            <small class="text-muted d-block mb-3" style="font-size: 12px;">
                                {{ $combo->description ?: 'Combo promocional gastronómico.' }}
                            </small>

                            <!-- LISTA VISUAL DE COMPONENTES DE LA RECETA -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">
                                        Receta / Componentes Incluidos:
                                    </small>
                                    <small class="text-muted">{{ $combo->comboItems->count() }} ítems</small>
                                </div>

                                @forelse($combo->comboItems as $ci)
                                    @php $cp = $ci->componentProduct; @endphp
                                    @if($cp)
                                        @if($cp->item_type === 'PRODUCTO')
                                            <div class="combo-chip combo-chip-kardex">
                                                <i class="fas fa-wine-bottle text-success"></i>
                                                <span><strong>{{ floatval($ci->quantity) }}x</strong> {{ $cp->name }}</span>
                                                <span class="badge badge-success text-white ml-auto" style="font-size: 9.5px; padding: 2px 6px;">
                                                    Kardex: -{{ floatval($ci->quantity) }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="combo-chip combo-chip-kitchen">
                                                <i class="fas fa-utensils text-primary"></i>
                                                <span><strong>{{ floatval($ci->quantity) }}x</strong> {{ $cp->name }}</span>
                                                <span class="badge badge-light border text-primary ml-auto" style="font-size: 9.5px; padding: 2px 6px;">
                                                    Cocina
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                @empty
                                    <div class="alert alert-warning py-1 px-2 small mb-0">Sin componentes registrados</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- RECUADRO FINANCIERO Y DE PRECIOS -->
                        <div class="combo-finance-box">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="small text-muted d-block" style="font-size: 11px;">Precio Combo:</span>
                                    <span class="h4 font-weight-bold text-primary mb-0">{{ $sym }} {{ number_format($combo->sell_price, 2) }}</span>
                                </div>
                                @if($saving > 0)
                                    <div class="text-right">
                                        <span class="small text-muted d-block" style="font-size: 11px;">Individual: <del>{{ $sym }} {{ number_format($totalComponentsNormal, 2) }}</del></span>
                                        <span class="badge badge-success font-weight-bold" style="font-size: 11px; padding: 3px 8px;">
                                            Ahorra: {{ $sym }} {{ number_format($saving, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top" style="border-color: #fef08a !important; font-size: 12px;">
                                <span class="text-muted">Costo receta: <strong>{{ $sym }} {{ number_format($combo->purchase_price, 2) }}</strong></span>
                                <span class="text-success font-weight-bold">
                                    Utilidad: +{{ $sym }} {{ number_format($profit, 2) }} ({{ $marginPct }}%)
                                </span>
                            </div>
                        </div>

                        <!-- BOTONES DE ACCIÓN RÁPIDA -->
                        <div class="d-flex mt-3" style="gap: 8px;">
                            <a href="{{ route('products.edit', $combo) }}" class="btn btn-warning text-dark btn-sm font-weight-bold flex-grow-1 shadow-sm">
                                <i class="fas fa-sliders-h mr-1"></i> Modificar Receta
                            </a>
                            <a href="{{ route('products.show', $combo) }}" class="btn btn-outline-secondary btn-sm" title="Ver ficha y trazabilidad">
                                <i class="far fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white rounded border">
                <i class="fas fa-gifts fa-3x text-warning mb-2 d-block"></i>
                <h5 class="font-weight-bold">No hay combos registrados todavía</h5>
                <p class="text-muted small">Crea tu primer combo promocional para aumentar el ticket promedio de tu restaurante.</p>
                <a href="{{ route('products.create', ['type' => 'COMBO']) }}" class="btn btn-warning text-dark font-weight-bold">
                    <i class="fas fa-plus mr-1"></i> Crear Primer Combo
                </a>
            </div>
        @endforelse
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- VISTA 2: TABLA TRADICIONAL (LIMPIA Y OPTIMIZADA)                          -->
    <!-- ========================================================================= -->
    <div id="combosTableView" class="row" style="{{ (($typeFilter ?? '') === 'COMBO') ? 'display:none;' : '' }}">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="order-listing" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Imagen</th>
                                    <th>Descripción / Ítem</th>
                                    <th>Categoría / Estación</th>
                                    <th>Costo / Venta</th>
                                    <th class="text-center">Existencia / Control</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <span class="badge badge-light border font-weight-bold" style="font-size: 12px;">{{ $product->code ?? $product->id }}</span>
                                        @if($product->sku)
                                            <br><small class="text-primary font-weight-bold">{{ $product->sku }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="width: 54px; height: 54px; border-radius: 10px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                            @if($product->image && file_exists(public_path('image/'.$product->image)))
                                                <img src="{{ asset('image/'.$product->image) }}" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="{{ $product->name }}" />
                                            @else
                                                <i class="fas {{ $product->item_type === 'COMBO' ? 'fa-gifts text-warning' : ($product->isService() ? 'fa-concierge-bell text-info' : 'fa-box text-secondary') }} fa-lg"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{route('products.show',$product)}}" class="font-weight-bold text-dark" style="font-size: 13.5px;">{{$product->name}}</a>
                                        <div class="mt-1">
                                            @if($product->item_type === 'COMBO')
                                                <span class="badge badge-warning text-dark font-weight-bold" style="font-size: 10.5px; padding: 3px 8px;">
                                                    <i class="fas fa-gifts mr-1"></i> Combo Promocional
                                                </span>
                                                @if($product->comboItems && $product->comboItems->isNotEmpty())
                                                    <div class="mt-1 small text-muted" style="font-size: 11.5px; line-height: 1.4;">
                                                        @foreach($product->comboItems as $ci)
                                                            @if($ci->componentProduct)
                                                                <div>
                                                                    &bull; <strong>{{ floatval($ci->quantity) }}x</strong> {{ $ci->componentProduct->name }}
                                                                    @if($ci->componentProduct->item_type === 'PRODUCTO')
                                                                        <span class="text-success font-weight-bold" style="font-size: 10.5px;">(Stock -{{ floatval($ci->quantity) }})</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @elseif($product->isService())
                                                <span class="badge badge-info" style="font-size: 10.5px;">Plato / Cocina</span>
                                            @elseif($product->location)
                                                <small class="text-muted"><i class="fas fa-map-pin mr-1"></i>{{ $product->location }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-dark" style="font-size: 13px;">{{$product->category->name ?? '-'}}</strong><br>
                                        <small class="text-muted">{{$product->brand->name ?? '-'}}</small>
                                    </td>
                                    <td>
                                        <strong class="text-primary font-weight-bold" style="font-size: 14px;">{{ $sym }} {{ number_format($product->sell_price, 2) }}</strong><br>
                                        @if($product->purchase_price > 0)
                                            <small class="text-muted">Costo: {{ $sym }} {{ number_format($product->purchase_price, 2) }}</small>
                                            <br><small class="text-success font-weight-bold">Ganancia: +{{ $sym }} {{ number_format($product->sell_price - $product->purchase_price, 2) }}</small>
                                        @endif
                                    </td>
                                    <td align="center">
                                        @if($product->item_type === 'COMBO')
                                            <span class="badge badge-light border text-dark font-weight-bold" style="font-size: 11px;">
                                                <i class="fas fa-boxes text-warning mr-1"></i> Descuenta Stock
                                            </span>
                                            <small class="d-block text-muted mt-1" style="font-size: 10px;">Automático en Kardex</small>
                                        @elseif($product->isService())
                                            <span class="badge badge-outline-info" style="font-size: 11px;">Plato Ilimitado</span>
                                        @else
                                            @if ($product->stock <= 0)
                                                <span class="badge badge-danger px-2 py-1" style="font-size: 11px;">
                                                    Agotado (0 {{ $product->unit_measure ?? 'UND' }})
                                                </span>
                                            @elseif ($product->stock <= ($product->stock_min ?? 5))
                                                <span class="badge badge-warning text-dark px-2 py-1" style="font-size: 11px;">
                                                    Bajo ({{$product->stock}} {{ $product->unit_measure ?? 'UND' }})
                                                </span>
                                            @else
                                                <span class="badge badge-success px-2 py-1" style="font-size: 11px;">
                                                    {{$product->stock}} {{ $product->unit_measure ?? 'UND' }}
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->status == 'ACTIVO')
                                            <a class="badge badge-success py-1 px-2" href="{{route('change.status.products', $product)}}" style="text-decoration:none;" title="Clic para desactivar">
                                                ✓ ACTIVO
                                            </a>
                                        @else
                                            <a class="badge badge-danger py-1 px-2" href="{{route('change.status.products', $product)}}" style="text-decoration:none;" title="Clic para activar">
                                                ✕ INACTIVO
                                            </a>
                                        @endif
                                    </td>
                                    <td align="center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a class="btn btn-outline-warning" href="{{route('products.edit', $product)}}" title="Editar receta / datos">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-info" href="{{route('products.show', $product)}}" title="Ver Ficha y Kardex">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
<script>
    function toggleComboView(mode) {
        if (mode === 'cards') {
            $('#combosCardView').fadeIn(150);
            $('#combosTableView').hide();
            $('#btnViewCards').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btnViewTable').removeClass('btn-primary').addClass('btn-outline-primary');
            localStorage.setItem('combo_view_mode', 'cards');
        } else {
            $('#combosCardView').hide();
            $('#combosTableView').fadeIn(150);
            $('#btnViewTable').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btnViewCards').removeClass('btn-primary').addClass('btn-outline-primary');
            localStorage.setItem('combo_view_mode', 'table');
        }
    }

    $(document).ready(function() {
        var savedMode = localStorage.getItem('combo_view_mode');
        @if(($typeFilter ?? '') === 'COMBO')
            if (savedMode === 'table') {
                toggleComboView('table');
            } else {
                toggleComboView('cards');
            }
        @endif
    });
</script>
@endsection
