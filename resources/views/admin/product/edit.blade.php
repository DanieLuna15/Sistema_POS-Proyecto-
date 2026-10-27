@extends('layouts.admin')
@section('title', 'Editar Producto o Servicio')

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
    $rubro = $activeCompany->rubro_config;
@endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <span class="badge badge-primary mb-1"><i class="{{ $rubro['icon'] }} mr-1"></i> Modo {{ $rubro['label'] }}</span>
            <h3 class="page-title mb-1">
                <i class="fas fa-edit text-warning mr-2"></i> Editar en {{ $rubro['product_title'] }}: {{ $product->name }}
            </h3>
            <small class="text-muted">Actualiza precios, código de barras, {{ strtolower($rubro['sku_label']) }}, {{ strtolower($rubro['location_label']) }} o imagen.</small>
        </div>
        <a href="{{route('products.index')}}" class="btn btn-light">
            <i class="fas fa-arrow-left mr-1"></i> Volver al Catálogo
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($product, ['route'=>['products.update', $product], 'method'=>'PUT', 'files' => true]) !!}

                    <h5 class="text-primary font-weight-bold mb-3">1. Identificación y Clasificación ({{ $rubro['short_label'] }})</h5>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Tipo de Ítem *</label>
                            <select name="item_type" class="form-control">
                                <option value="PRODUCTO" {{ old('item_type', $product->item_type) == 'PRODUCTO' ? 'selected' : '' }}>Producto Físico (Controla Stock)</option>
                                <option value="SERVICIO" {{ old('item_type', $product->item_type) == 'SERVICIO' ? 'selected' : '' }}>Servicio / Plato / Mano de Obra (Sin Stock)</option>
                                <option value="COMBO" {{ old('item_type', $product->item_type) == 'COMBO' ? 'selected' : '' }}>Combo / Kit / Promoción</option>
                            </select>
                        </div>
                        <div class="col-md-5 form-group">
                            <label class="font-weight-bold">Nombre Comercial / Descripción *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="font-weight-bold">Código de Barras</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                                   value="{{ old('code', $product->code) }}">
                            @error('code')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="font-weight-bold text-primary">{{ $rubro['sku_label'] }}</label>
                            <input type="text" class="form-control" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="{{ $rubro['sku_placeholder'] }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Unidad de Medida *</label>
                            <select name="unit_measure" class="form-control">
                                @foreach($units as $uCode => $uLabel)
                                    <option value="{{ $uCode }}" {{ old('unit_measure', $product->unit_measure ?? 'UND') == $uCode ? 'selected' : '' }}>{{ $uLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Categoría *</label>
                            <select class="js-example-basic-single w-100 form-control" name="category_id">
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">{{ $rubro['brand_label'] }} *</label>
                            <select class="js-example-basic-single w-100 form-control" name="brand_id">
                                @foreach($brands as $brand)
                                    <option value="{{$brand->id}}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{$brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Proveedor Principal *</label>
                            <select class="js-example-basic-single w-100 form-control" name="provider_id">
                                @foreach($providers as $provider)
                                    <option value="{{$provider->id}}" {{ old('provider_id', $product->provider_id) == $provider->id ? 'selected' : '' }}>{{$provider->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h5 class="text-primary font-weight-bold mb-3">2. Precios, Costos y Alertas</h5>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Precio de Costo / Compra ({{ $sym }})</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="purchase_price"
                                   value="{{ old('purchase_price', $product->purchase_price ?? 0) }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-success">Precio de Venta Público ({{ $sym }}) *</label>
                            <input type="number" step="0.01" min="0.01" class="form-control @error('sell_price') is-invalid @enderror" name="sell_price"
                                   value="{{ old('sell_price', $product->sell_price) }}" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Precio Mayorista ({{ $sym }})</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="wholesale_price"
                                   value="{{ old('wholesale_price', $product->wholesale_price) }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Alerta de Stock Mínimo</label>
                            <input type="number" step="1" min="0" class="form-control" name="stock_min"
                                   value="{{ old('stock_min', $product->stock_min ?? 5) }}">
                        </div>
                    </div>

                    <hr>
                    <h5 class="text-primary font-weight-bold mb-3">3. Atributos Específicos de {{ $rubro['short_label'] }}</h5>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">{{ $rubro['location_label'] }}</label>
                            <input type="text" class="form-control" name="location" value="{{ old('location', $product->location) }}" placeholder="{{ $rubro['location_placeholder'] }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">{{ $rubro['batch_label'] }}</label>
                            <input type="text" class="form-control" name="batch_number" value="{{ old('batch_number', $product->batch_number) }}" placeholder="{{ $rubro['batch_placeholder'] }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" name="expiration_date" value="{{ old('expiration_date', $product->expiration_date) }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Garantía (Días)</label>
                            <input type="number" min="0" class="form-control" name="warranty_days" value="{{ old('warranty_days', $product->warranty_days) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5 form-group">
                            <label class="font-weight-bold">Descripción / Especificaciones</label>
                            <textarea name="description" class="form-control" rows="5">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Cambiar Imagen (Opcional)</label>
                            <input accept="image/*" type="file" name="picture" id="picture" class="dropify" />
                        </div>
                        <div class="col-md-3 form-group text-center">
                            <label class="font-weight-bold d-block">Imagen Actual</label>
                            @if($product->image && file_exists(public_path('image/'.$product->image)))
                                <img src="{{asset('image/'.$product->image)}}" class="img-fluid rounded border p-1" style="max-height: 150px;">
                            @else
                                <div class="p-4 bg-light rounded border text-muted">
                                    <i class="fas fa-box fa-3x mb-2"></i><br>Sin imagen
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-lg mr-2">
                            <i class="fas fa-save mr-1"></i> Guardar Cambios
                        </button>
                        <a href="{{route('products.index')}}" class="btn btn-light btn-lg">Cancelar</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! Html::script('melody/js/dropify.js') !!}
{!! Html::script('melody/js/select2.js') !!}
@endsection
