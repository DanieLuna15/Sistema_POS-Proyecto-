@extends('layouts.admin')
@section('title', 'Catálogo de Productos y Servicios')

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
    $rubro = $activeCompany->rubro_config;
@endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <span class="badge badge-primary mb-1"><i class="{{ $rubro['icon'] }} mr-1"></i> Rubro: {{ $rubro['label'] }}</span>
            <h3 class="page-title mb-1">
                <i class="fas fa-boxes text-primary mr-2"></i> {{ $rubro['product_title'] }}
            </h3>
            <small class="text-muted">{{ $rubro['desc'] }}</small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap" style="gap: 8px;">
            <a href="{{route('transfers.index')}}" class="btn btn-outline-primary font-weight-bold">
                <i class="fas fa-truck-loading mr-1"></i> Traspasos entre Sucursales
            </a>
            @if($activeCompany->enable_kardex ?? true)
            <a href="{{route('kardex.index')}}" class="btn btn-outline-warning text-dark font-weight-bold">
                <i class="fas fa-clipboard-list mr-1"></i> Kardex y Ajustes
            </a>
            @endif
            <a href="{{route('products.create', ['type' => 'COMBO'])}}" class="btn btn-warning text-dark font-weight-bold shadow-sm">
                <i class="fas fa-gifts mr-1 text-danger"></i> + Nuevo Combo / Promoción
            </a>
            <a href="{{route('products.create')}}" class="btn btn-info font-weight-bold shadow-sm">
                <i class="fas fa-plus mr-1"></i> Registrar en {{ $rubro['short_label'] }}
            </a>
        </div>
    </div>

    <!-- PESTAÑAS DE FILTRO POR TIPO (TODOS, COMBOS, PLATOS/SERVICIOS, PRODUCTOS CON STOCK) -->
    <div class="mb-3 d-flex flex-wrap" style="gap: 8px;">
        <a href="{{ route('products.index') }}" class="btn btn-sm font-weight-bold {{ empty($typeFilter) ? 'btn-primary' : 'btn-outline-primary bg-white' }}">
            <i class="fas fa-th-large mr-1"></i> Todos los Ítems
        </a>
        <a href="{{ route('products.index', ['type' => 'COMBO']) }}" class="btn btn-sm font-weight-bold {{ ($typeFilter ?? '') === 'COMBO' ? 'btn-warning text-dark shadow-sm' : 'btn-outline-warning text-dark bg-white' }}">
            <i class="fas fa-gifts mr-1 text-danger"></i> Combos y Promociones
        </a>
        <a href="{{ route('products.index', ['type' => 'SERVICIO']) }}" class="btn btn-sm font-weight-bold {{ ($typeFilter ?? '') === 'SERVICIO' ? 'btn-info shadow-sm' : 'btn-outline-info bg-white' }}">
            <i class="fas fa-utensils mr-1"></i> Platos de Cocina / Servicios
        </a>
        <a href="{{ route('products.index', ['type' => 'PRODUCTO']) }}" class="btn btn-sm font-weight-bold {{ ($typeFilter ?? '') === 'PRODUCTO' ? 'btn-success shadow-sm' : 'btn-outline-success bg-white' }}">
            <i class="fas fa-box-open mr-1"></i> Bebidas / Productos con Stock (Kardex)
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="order-listing" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Código / {{ $rubro['sku_label'] }}</th>
                                    <th>Imagen</th>
                                    <th>Descripción / {{ $rubro['location_label'] }}</th>
                                    <th>Categoría / {{ $rubro['brand_label'] }}</th>
                                    <th>Costo / Venta</th>
                                    <th class="text-center">Existencia / Unidad</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <span class="badge badge-outline-dark font-weight-bold">{{ $product->code ?? $product->id }}</span>
                                        @if($product->sku)
                                            <br><small class="text-primary font-weight-bold">{{ $product->sku }}</small>
                                        @endif
                                        @if($product->batch_number)
                                            <br><small class="text-muted">{{ $rubro['batch_label'] }}: {{ $product->batch_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->image && file_exists(public_path('image/'.$product->image)))
                                            <img src="{{asset('image/'.$product->image)}}" class="img-lg rounded" alt="{{ $product->name }}" />
                                        @else
                                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded border text-primary" style="width: 46px; height: 46px;">
                                                <i class="fas {{ $product->isService() ? 'fa-concierge-bell' : 'fa-box' }} fa-lg"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('products.show',$product)}}" class="font-weight-bold">{{$product->name}}</a><br>
                                        @if($product->item_type === 'COMBO')
                                            <span class="badge badge-warning text-dark font-weight-bold">
                                                <i class="fas fa-gifts mr-1"></i> COMBO PROMOCIONAL
                                            </span>
                                            @if($product->comboItems && $product->comboItems->isNotEmpty())
                                                <div class="mt-1 p-1 px-2 rounded border bg-light small">
                                                    <strong class="text-dark d-block" style="font-size:11px;">Incluye:</strong>
                                                    @foreach($product->comboItems as $ci)
                                                        @if($ci->componentProduct)
                                                            <span class="d-block text-muted" style="font-size:11px;">
                                                                &bull; <strong>{{ floatval($ci->quantity) }}x</strong> {{ $ci->componentProduct->name }}
                                                                @if($ci->componentProduct->item_type === 'PRODUCTO')
                                                                    <span class="text-success font-weight-bold">(Descuenta Stock)</span>
                                                                @endif
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        @elseif($product->isService())
                                            <span class="badge badge-info">SERVICIO / PLATO COCINA</span>
                                        @elseif($product->location)
                                            <small class="text-muted"><i class="fas fa-map-pin mr-1"></i>{{ $product->location }}</small>
                                        @endif
                                        @if($product->expiration_date)
                                            @php
                                                $expDate = \Carbon\Carbon::parse($product->expiration_date);
                                                $daysLeft = now()->diffInDays($expDate, false);
                                            @endphp
                                            <br>
                                            @if($daysLeft < 0)
                                                <span class="badge badge-danger"><i class="fas fa-skull-crossbones mr-1"></i>VENCIDO: {{ $expDate->format('d/m/Y') }}</span>
                                            @elseif($daysLeft <= 60)
                                                <span class="badge badge-warning text-dark"><i class="fas fa-exclamation-triangle mr-1"></i>Vence en {{ $daysLeft }} días ({{ $expDate->format('d/m/Y') }})</span>
                                            @else
                                                <small class="text-success"><i class="fas fa-calendar-check mr-1"></i>Vence: {{ $expDate->format('d/m/Y') }}</small>
                                            @endif
                                        @endif
                                        @if($product->warranty_days > 0)
                                            <br><small class="text-info font-weight-bold"><i class="fas fa-shield-alt mr-1"></i>Garantía: {{ $product->warranty_days }} días</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{$product->category->name ?? '-'}}</strong><br>
                                        <small class="text-muted">{{$product->brand->name ?? '-'}}</small>
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ $sym }} {{ number_format($product->sell_price, 2) }}</strong><br>
                                        @if($product->purchase_price > 0)
                                            <small class="text-muted">Costo: {{ $sym }} {{ number_format($product->purchase_price, 2) }}</small>
                                            <br><small class="text-primary font-weight-bold">Ganancia: +{{ $sym }} {{ number_format($product->sell_price - $product->purchase_price, 2) }}</small>
                                        @endif
                                    </td>
                                    <td align="center">
                                        @if($product->item_type === 'COMBO')
                                            <span class="badge badge-warning text-dark font-weight-bold">Descuenta Componentes</span>
                                            <div class="small text-muted mt-1" style="font-size:10px;">Automático en Kardex</div>
                                        @elseif($product->isService())
                                            <span class="badge badge-outline-info">Plato / Ilimitado</span>
                                        @else
                                            @if ($product->stock <= 0)
                                                <span class="btn btn-danger btn-sm btn-rounded" title="Stock en {{ $activeBranch->name ?? 'Sucursal Activa' }}">
                                                    <strong>{{$product->stock}}</strong> {{ $product->unit_measure ?? 'UND' }}
                                                </span>
                                            @elseif ($product->stock <= ($product->stock_min ?? 5))
                                                <span class="btn btn-warning btn-sm btn-rounded" title="Stock en {{ $activeBranch->name ?? 'Sucursal Activa' }}">
                                                    <strong>{{$product->stock}}</strong> {{ $product->unit_measure ?? 'UND' }}
                                                </span>
                                            @else
                                                <span class="btn btn-success btn-sm btn-rounded" title="Stock en {{ $activeBranch->name ?? 'Sucursal Activa' }}">
                                                    <strong>{{$product->stock}}</strong> {{ $product->unit_measure ?? 'UND' }}
                                                </span>
                                            @endif
                                            <div class="small text-muted mt-1" style="font-size:11px;">
                                                <i class="fas fa-store-alt text-primary"></i> {{ $activeBranch->name ?? 'Sucursal Activa' }}
                                                @if($product->branchStocks && $product->branchStocks->count() > 1)
                                                    <br><span class="text-dark font-weight-bold">Total Emp: {{ $product->total_company_stock }} {{ $product->unit_measure ?? 'UND' }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->status == 'ACTIVO')
                                            <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.products', $product)}}">
                                                ACTIVO <i class="fas fa-check"></i>
                                            </a>
                                        @else
                                            <a class="jsgrid-button btn btn-danger btn-sm btn-block" href="{{route('change.status.products', $product)}}">
                                                INACTIVO <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td align="center">
                                        <a class="btn btn-outline-warning btn-sm" href="{{route('products.edit', $product)}}" title="Editar">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        <a class="btn btn-outline-info btn-sm" href="{{route('products.show', $product)}}" title="Ver Ficha y Kardex">
                                            <i class="far fa-eye"></i>
                                        </a>
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
@endsection
