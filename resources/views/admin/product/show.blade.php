@extends('layouts.admin')
@section('title', 'Ficha del Producto: ' . $product->name)

@section('content')
@php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title mb-1">
                Ficha Técnica y Kardex: {{$product->name}}
            </h3>
            <small class="text-muted">Código: <strong>{{ $product->code }}</strong> &bull; Categoría: {{ $product->category->name ?? '-' }}</small>
        </div>
        <div>
            <a href="{{route('products.edit', $product)}}" class="btn btn-warning mr-1">
                <i class="fas fa-edit mr-1"></i> Editar Producto
            </a>
            <a href="{{route('products.index')}}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center">
                    @if($product->image && file_exists(public_path('image/'.$product->image)))
                        <img src="{{asset('image/'.$product->image)}}" alt="{{$product->name}}" class="img-lg mb-3 rounded" style="width: 140px; height: 140px; object-fit: contain;" />
                    @else
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center bg-light rounded border text-primary" style="width: 120px; height: 120px;">
                            <i class="fas fa-box-open fa-3x"></i>
                        </div>
                    @endif
                    <h4 class="font-weight-bold">{{$product->name}}</h4>
                    <span class="badge badge-primary mb-3">{{ $product->item_type ?? 'PRODUCTO' }} ({{ $product->unit_measure ?? 'UND' }})</span>

                    <div class="text-left border-top pt-3">
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">Código de Barras:</span>
                            <span class="float-right text-dark">{{$product->code}}</span>
                        </p>
                        @if($product->sku)
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">SKU / Referencia:</span>
                            <span class="float-right text-muted">{{$product->sku}}</span>
                        </p>
                        @endif
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">Precio de Costo:</span>
                            <span class="float-right text-muted">{{ $sym }} {{ number_format($product->purchase_price ?? 0, 2) }}</span>
                        </p>
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">Precio de Venta:</span>
                            <span class="float-right text-success font-weight-bold">{{ $sym }} {{ number_format($product->sell_price, 2) }}</span>
                        </p>
                        @if($product->wholesale_price)
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">Precio Mayorista:</span>
                            <span class="float-right text-info">{{ $sym }} {{ number_format($product->wholesale_price, 2) }}</span>
                        </p>
                        @endif
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">Stock Disponible:</span>
                            <span class="float-right font-weight-bold">{{ $product->isService() ? 'Servicio' : $product->stock . ' ' . ($product->unit_measure ?? 'UND') }}</span>
                        </p>
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">Ubicación / Estante:</span>
                            <span class="float-right text-muted">{{ $product->location ?? 'No especificada' }}</span>
                        </p>
                        @if($product->batch_number || $product->expiration_date)
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">Lote / Vencimiento:</span>
                            <span class="float-right text-danger">{{ $product->batch_number ?? '-' }} ({{ $product->expiration_date ?? '-' }})</span>
                        </p>
                        @endif
                        <p class="clearfix mb-2">
                            <span class="float-left font-weight-bold">Proveedor:</span>
                            <span class="float-right">
                                <a href="{{route('providers.show',$product->provider->id)}}">{{$product->provider->name}}</a>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    @if(!$product->isService())
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="card-title mb-0"><i class="fas fa-store-alt text-primary mr-1"></i> Stock Independiente por Sucursal</h4>
                        <a href="{{ route('transfers.index') }}" class="btn btn-outline-primary btn-xs font-weight-bold">
                            <i class="fas fa-truck-loading mr-1"></i> Hacer Traspaso entre Sucursales
                        </a>
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Sucursal</th>
                                    <th>Ubicación</th>
                                    <th class="text-center">Stock en Sucursal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->branchStocks as $bs)
                                <tr class="{{ (isset($activeBranch) && $activeBranch->id == $bs->branch_id) ? 'table-primary' : '' }}">
                                    <td>
                                        <strong>{{ $bs->branch->name ?? 'Sucursal #'.$bs->branch_id }}</strong>
                                        @if(isset($activeBranch) && $activeBranch->id == $bs->branch_id)
                                            <span class="badge badge-primary ml-1">Activa</span>
                                        @endif
                                    </td>
                                    <td>{{ $bs->location ?: ($product->location ?: '-') }}</td>
                                    <td class="text-center font-weight-bold">{{ $bs->stock }} {{ $product->unit_measure ?? 'UND' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td><strong>{{ $activeBranch->name ?? 'Casa Matriz' }}</strong></td>
                                    <td>{{ $product->location ?: '-' }}</td>
                                    <td class="text-center font-weight-bold">{{ $product->stock }} {{ $product->unit_measure ?? 'UND' }}</td>
                                </tr>
                                @endforelse
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="2" class="text-right">STOCK TOTAL EMPRESA (TODAS LAS SUCURSALES):</td>
                                    <td class="text-center text-success">{{ $product->total_company_stock }} {{ $product->unit_measure ?? 'UND' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <h4 class="card-title"><i class="fas fa-history text-primary mr-1"></i> Últimos Movimientos en Kardex de este Producto</h4>
                    @if($product->description)
                        <div class="alert alert-secondary mb-3">
                            <strong>Descripción / Especificaciones:</strong> {{ $product->description }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Referencia</th>
                                    <th>Movimiento</th>
                                    <th class="text-center">Stock Ant.</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-center">Saldo</th>
                                    <th>Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->movements->take(20) as $mov)
                                @php $isEntry = $mov->isEntry(); @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($mov->movement_date)->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge badge-outline-dark">{{ $mov->reference_code }}</span></td>
                                    <td>
                                        <span class="badge {{ $isEntry ? 'badge-success' : 'badge-danger' }}">{{ $mov->movement_type }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($mov->previous_stock, 0) }}</td>
                                    <td class="text-center font-weight-bold {{ $isEntry ? 'text-success' : 'text-danger' }}">
                                        {{ $isEntry ? '+' : '-' }}{{ number_format($mov->quantity, 0) }}
                                    </td>
                                    <td class="text-center font-weight-bold">{{ number_format($mov->new_stock, 0) }}</td>
                                    <td>{{ $mov->notes }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No hay movimientos de Kardex registrados aún para este ítem.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
