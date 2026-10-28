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
                            <select name="item_type" id="edit_item_type" class="form-control" onchange="toggleEditComboSection()">
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
                            <input type="number" step="0.01" min="0" class="form-control" name="purchase_price" id="edit_purchase_price"
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

                    <!-- SECCIÓN DE COMPONENTES DEL COMBO EN EDICIÓN -->
                    <div id="editComboBuilderSection" class="p-3 rounded border mb-3" style="display: {{ old('item_type', $product->item_type) === 'COMBO' ? 'block' : 'none' }}; background: #fffbeb; border-color: #f59e0b !important;">
                        <h5 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-gifts text-warning mr-1"></i> Receta / Componentes del Combo (Control Financiero y Descuento de Stock)
                        </h5>
                        <small class="text-muted d-block mb-2">
                            Al vender este Combo, el sistema descuenta automáticamente del Kardex los <strong>Productos con Stock (ej. Gaseosas)</strong> y envía los <strong>Platos a Cocina</strong>.
                        </small>

                        <div class="row align-items-end mb-2">
                            <div class="col-md-7 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold">Agregar Plato o Bebida al Combo:</label>
                                <select id="edit_combo_picker_select" class="form-control form-control-sm">
                                    <option value="">-- Seleccione un componente --</option>
                                    @foreach($availableComponents ?? [] as $comp)
                                        <option value="{{ $comp->id }}"
                                                data-name="{{ $comp->name }}"
                                                data-type="{{ $comp->item_type }}"
                                                data-cost="{{ floatval($comp->purchase_price) }}"
                                                data-price="{{ floatval($comp->sell_price) }}">
                                            {{ $comp->name }} — [{{ $comp->item_type === 'SERVICIO' ? 'Plato Cocina' : 'Stock: '.$comp->stock }}] (Costo: {{ $sym }} {{ number_format($comp->purchase_price, 2) }} | Venta: {{ $sym }} {{ number_format($comp->sell_price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold">Cantidad:</label>
                                <input type="number" id="edit_combo_picker_qty" class="form-control form-control-sm text-center font-weight-bold" value="1" min="1" step="1">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-warning text-dark btn-sm btn-block font-weight-bold" onclick="addEditComboComponent()">
                                    <i class="fas fa-plus-circle mr-1"></i> Incluir en el Combo
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive bg-white rounded border">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Componente del Combo</th>
                                        <th>Comportamiento de Stock</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-right">Costo Total</th>
                                        <th class="text-right">Precio Normal</th>
                                        <th class="text-center" style="width: 60px;">Quitar</th>
                                    </tr>
                                </thead>
                                <tbody id="editComboComponentsBody">
                                    @forelse($product->comboItems as $cItem)
                                        @php
                                            $cp = $cItem->componentProduct;
                                        @endphp
                                        @if($cp)
                                            <tr id="edit_combo_row_{{ $cp->id }}"
                                                data-cost="{{ floatval($cp->purchase_price) }}"
                                                data-price="{{ floatval($cp->sell_price) }}"
                                                data-qty="{{ floatval($cItem->quantity) }}">
                                                <td class="font-weight-bold">
                                                    {{ $cp->name }}
                                                    <input type="hidden" name="combo_component_id[]" value="{{ $cp->id }}">
                                                </td>
                                                <td>
                                                    @if($cp->item_type === 'SERVICIO')
                                                        <span class="badge badge-info">🍽️ Plato Cocina (Sin límite de stock)</span>
                                                    @else
                                                        <span class="badge badge-success">📦 Descuenta {{ floatval($cItem->quantity) }} u. del Kardex al vender</span>
                                                    @endif
                                                </td>
                                                <td class="text-center font-weight-bold">
                                                    {{ floatval($cItem->quantity) }}
                                                    <input type="hidden" name="combo_component_qty[]" value="{{ floatval($cItem->quantity) }}">
                                                </td>
                                                <td class="text-right">{{ $sym }} {{ number_format($cp->purchase_price * $cItem->quantity, 2) }}</td>
                                                <td class="text-right text-muted">{{ $sym }} {{ number_format($cp->sell_price * $cItem->quantity, 2) }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="document.getElementById('edit_combo_row_{{ $cp->id }}').remove(); recalcEditComboCost();">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
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
<script>
    function toggleEditComboSection() {
        var t = document.getElementById('edit_item_type').value;
        document.getElementById('editComboBuilderSection').style.display = (t === 'COMBO') ? 'block' : 'none';
    }

    function addEditComboComponent() {
        var sel = document.getElementById('edit_combo_picker_select');
        if (!sel || !sel.value) return;
        var opt = sel.options[sel.selectedIndex];
        var compId = sel.value;
        var name = opt.getAttribute('data-name');
        var type = opt.getAttribute('data-type');
        var cost = parseFloat(opt.getAttribute('data-cost')) || 0;
        var price = parseFloat(opt.getAttribute('data-price')) || 0;
        var qty = Math.max(1, parseFloat(document.getElementById('edit_combo_picker_qty').value) || 1);

        var existing = document.getElementById('edit_combo_row_' + compId);
        if (existing) existing.remove();

        var stockBadge = (type === 'SERVICIO')
            ? '<span class="badge badge-info">🍽️ Plato Cocina (Sin límite de stock)</span>'
            : '<span class="badge badge-success">📦 Descuenta ' + qty + ' u. del Kardex al vender</span>';

        var tr = document.createElement('tr');
        tr.id = 'edit_combo_row_' + compId;
        tr.setAttribute('data-cost', cost);
        tr.setAttribute('data-price', price);
        tr.setAttribute('data-qty', qty);
        tr.innerHTML =
            '<td class="font-weight-bold">' + name + '<input type="hidden" name="combo_component_id[]" value="' + compId + '"></td>' +
            '<td>' + stockBadge + '</td>' +
            '<td class="text-center font-weight-bold">' + qty + '<input type="hidden" name="combo_component_qty[]" value="' + qty + '"></td>' +
            '<td class="text-right">{{ $sym }} ' + (cost * qty).toFixed(2) + '</td>' +
            '<td class="text-right text-muted">{{ $sym }} ' + (price * qty).toFixed(2) + '</td>' +
            '<td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger" onclick="document.getElementById(\'edit_combo_row_' + compId + '\').remove(); recalcEditComboCost();"><i class="fas fa-trash-alt"></i></button></td>';

        document.getElementById('editComboComponentsBody').appendChild(tr);
        sel.value = '';
        document.getElementById('edit_combo_picker_qty').value = 1;
        recalcEditComboCost();
    }

    function recalcEditComboCost() {
        var totalCost = 0;
        document.querySelectorAll('#editComboComponentsBody tr[data-cost]').forEach(function(tr) {
            var c = parseFloat(tr.getAttribute('data-cost')) || 0;
            var q = parseFloat(tr.getAttribute('data-qty')) || 1;
            totalCost += (c * q);
        });
        document.getElementById('edit_purchase_price').value = totalCost.toFixed(2);
    }
</script>
@endsection
