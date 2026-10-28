@extends('layouts.admin')
@section('title', 'Registrar Producto o Servicio')

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
    $rubro = $activeCompany->rubro_config;
    $defaultItemType = old('item_type', request('type', $rubro['default_item_type'] ?? 'PRODUCTO'));
    $defaultUnit = old('unit_measure', request('type') === 'COMBO' ? 'COMBO' : ($rubro['default_unit'] ?? 'UND'));
@endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <span class="badge badge-primary mb-1"><i class="{{ $rubro['icon'] }} mr-1"></i> Modo {{ $rubro['label'] }}</span>
            <h3 class="page-title mb-1">
                <i class="fas fa-box-open text-primary mr-2"></i> Registrar en: {{ $rubro['product_title'] }}
            </h3>
            <small class="text-muted">{{ $rubro['desc'] }}</small>
        </div>
        <a href="{{route('products.index')}}" class="btn btn-light">
            <i class="fas fa-arrow-left mr-1"></i> Volver al Catálogo
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {!! Form::open(['route'=>'products.store', 'method'=>'POST', 'files' => true]) !!}

                    <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-tag mr-1"></i> 1. Identificación y Clasificación ({{ $rubro['short_label'] }})</h5>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Tipo de Ítem *</label>
                            <select name="item_type" id="item_type" class="form-control" onchange="toggleServiceFields()">
                                <option value="PRODUCTO" {{ $defaultItemType == 'PRODUCTO' ? 'selected' : '' }}>Producto Físico (Controla Stock)</option>
                                @if($activeCompany->enable_services ?? true)
                                <option value="SERVICIO" {{ $defaultItemType == 'SERVICIO' ? 'selected' : '' }}>Servicio / Plato / Mano de Obra (Sin Stock)</option>
                                <option value="COMBO" {{ $defaultItemType == 'COMBO' ? 'selected' : '' }}>Combo / Kit / Promoción</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-5 form-group">
                            <label class="font-weight-bold">Nombre Comercial / Descripción Principal *</label>
                            <input autofocus type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                   value="{{old('name')}}" placeholder="Nombre del artículo, medicamento, platillo o servicio..." required>
                            @error('name')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="font-weight-bold">Código de Barras</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                                   value="{{old('code')}}" placeholder="Escanear o Auto">
                            @error('code')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="font-weight-bold text-primary">{{ $rubro['sku_label'] }}</label>
                            <input type="text" class="form-control" name="sku" value="{{old('sku')}}" placeholder="{{ $rubro['sku_placeholder'] }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Unidad de Medida *</label>
                            <select name="unit_measure" class="form-control">
                                @foreach($units as $uCode => $uLabel)
                                    <option value="{{ $uCode }}" {{ $defaultUnit == $uCode ? 'selected' : '' }}>{{ $uLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Categoría *</label>
                            <select class="js-example-basic-single w-100 form-control" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">{{ $rubro['brand_label'] }} *</label>
                            <select class="js-example-basic-single w-100 form-control" name="brand_id" required>
                                @foreach($brands as $brand)
                                    <option value="{{$brand->id}}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{$brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Proveedor Principal *</label>
                            <select class="js-example-basic-single w-100 form-control" name="provider_id" required>
                                @foreach($providers as $provider)
                                    <option value="{{$provider->id}}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>{{$provider->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-dollar-sign mr-1"></i> 2. Precios, Costos y Stock Inicial</h5>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Precio de Costo / Compra ({{ $sym }})</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="purchase_price" id="purchase_price"
                                   value="{{old('purchase_price', '0.00')}}" oninput="calcMargin()">
                            <small class="text-muted">Para calcular utilidad real</small>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-success">Precio de Venta Público ({{ $sym }}) *</label>
                            <input type="number" step="0.01" min="0.01" class="form-control @error('sell_price') is-invalid @enderror" name="sell_price" id="sell_price"
                                   value="{{old('sell_price')}}" placeholder="0.00" oninput="calcMargin()" required>
                            <small id="margin_badge" class="text-info font-weight-bold">Margen: -</small>
                            @error('sell_price')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        @if($activeCompany->enable_wholesale ?? true)
                        <div class="col-md-2 form-group">
                            <label class="font-weight-bold">Precio Mayorista ({{ $sym }})</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="wholesale_price" value="{{old('wholesale_price')}}" placeholder="Opcional">
                        </div>
                        @endif
                        <div class="col-md-2 form-group stock-field">
                            <label class="font-weight-bold">Stock Inicial</label>
                            <input type="number" step="1" min="0" class="form-control" name="stock" value="{{old('stock', 0)}}">
                            <small class="text-muted">Se registra en Kardex</small>
                        </div>
                        <div class="col-md-2 form-group stock-field">
                            <label class="font-weight-bold">Alerta Stock Mín.</label>
                            <input type="number" step="1" min="0" class="form-control" name="stock_min" value="{{old('stock_min', $activeCompany->default_stock_alert ?? 5)}}">
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- SECCIÓN EXCLUSIVA PARA COMBOS: RECETA, COSTO FINANCIERO Y DESCUENTO KARDEX -->
                    <!-- ========================================================================= -->
                    <div id="comboBuilderSection" class="p-3 rounded border mb-3" style="display: none; background: #fffbeb; border-color: #f59e0b !important;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-gifts text-warning mr-1"></i> Armador de Combo / Promoción (Control Financiero y de Stock)
                                </h5>
                                <small class="text-muted">
                                    Al vender este Combo, el sistema descontará automáticamente del Kardex los <strong>Productos con Stock (ej. Gaseosas, Insumos)</strong> y enviará los <strong>Platos a Cocina</strong>.
                                </small>
                            </div>
                            <span class="badge badge-warning text-dark font-weight-bold px-3 py-2 mt-1 mt-md-0" id="comboFinancialSummary">
                                Costo Receta: {{ $sym }} 0.00 | Precio Sugerido Normal: {{ $sym }} 0.00
                            </span>
                        </div>

                        <div class="row align-items-end mb-2">
                            <div class="col-md-7 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold">Seleccionar Plato / Bebida / Componente del Catálogo:</label>
                                <select id="combo_picker_select" class="form-control form-control-sm">
                                    <option value="">-- Seleccione un producto o plato para agregar al combo --</option>
                                    @foreach($availableComponents ?? [] as $comp)
                                        <option value="{{ $comp->id }}"
                                                data-name="{{ $comp->name }}"
                                                data-type="{{ $comp->item_type }}"
                                                data-cost="{{ floatval($comp->purchase_price) }}"
                                                data-price="{{ floatval($comp->sell_price) }}"
                                                data-stock="{{ intval($comp->stock) }}">
                                            {{ $comp->name }} — [{{ $comp->item_type === 'SERVICIO' ? 'Plato Cocina' : 'Stock: '.$comp->stock }}] (Costo: {{ $sym }} {{ number_format($comp->purchase_price, 2) }} | Venta: {{ $sym }} {{ number_format($comp->sell_price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold">Cantidad en el Combo:</label>
                                <input type="number" id="combo_picker_qty" class="form-control form-control-sm text-center font-weight-bold" value="1" min="1" step="1">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-warning text-dark btn-sm btn-block font-weight-bold" onclick="addComponentToCombo()">
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
                                <tbody id="comboComponentsBody">
                                    <tr id="emptyComboRow">
                                        <td colspan="6" class="text-center text-muted py-2 small">
                                            Agregue los platos y bebidas que componen este combo para calcular su costo financiero y descontar stock automáticamente.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>
                    <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-sliders-h mr-1"></i> 3. Atributos Específicos de {{ $rubro['short_label'] }}</h5>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">{{ $rubro['location_label'] }}</label>
                            <input type="text" class="form-control" name="location" value="{{old('location')}}" placeholder="{{ $rubro['location_placeholder'] }}">
                        </div>
                        @if(($activeCompany->enable_lots_expiry ?? true) || ($activeCompany->business_type === 'ROPA'))
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">{{ $rubro['batch_label'] }}</label>
                            <input type="text" class="form-control" name="batch_number" value="{{old('batch_number')}}" placeholder="{{ $rubro['batch_placeholder'] }}">
                        </div>
                        @endif
                        @if($activeCompany->enable_lots_expiry ?? true)
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-danger">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" name="expiration_date" value="{{old('expiration_date')}}">
                        </div>
                        @endif
                        @if($activeCompany->enable_serials_warranties ?? true)
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-info">Garantía (Días)</label>
                            <input type="number" min="0" class="form-control" name="warranty_days" value="{{old('warranty_days')}}" placeholder="Ej: 365">
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Descripción / Ficha Técnica / Principio Activo</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Especificaciones adicionales del producto o servicio...">{{old('description')}}</textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Imagen del Producto (Opcional)</label>
                            <input accept="image/*" type="file" name="picture" id="picture" class="dropify" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-lg mr-2">
                            <i class="fas fa-save mr-1"></i> Registrar en Catálogo
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
    function calcMargin() {
        var cost = parseFloat(document.getElementById('purchase_price').value) || 0;
        var sell = parseFloat(document.getElementById('sell_price').value) || 0;
        var badge = document.getElementById('margin_badge');
        if (cost > 0 && sell > 0) {
            var profit = sell - cost;
            var pct = ((profit / cost) * 100).toFixed(1);
            badge.innerHTML = 'Ganancia: +{{ $sym }} ' + profit.toFixed(2) + ' (' + pct + '%)';
        } else {
            badge.innerHTML = 'Margen: -';
        }
    }

    function toggleServiceFields() {
        var type = document.getElementById('item_type').value;
        document.querySelectorAll('.stock-field').forEach(function(el) {
            el.style.display = (type === 'SERVICIO' || type === 'COMBO') ? 'none' : 'block';
        });
        var comboSec = document.getElementById('comboBuilderSection');
        if (comboSec) {
            comboSec.style.display = (type === 'COMBO') ? 'block' : 'none';
        }
    }

    function addComponentToCombo() {
        var sel = document.getElementById('combo_picker_select');
        if (!sel || !sel.value) return;
        var opt = sel.options[sel.selectedIndex];
        var compId = sel.value;
        var name = opt.getAttribute('data-name');
        var type = opt.getAttribute('data-type');
        var cost = parseFloat(opt.getAttribute('data-cost')) || 0;
        var price = parseFloat(opt.getAttribute('data-price')) || 0;
        var qty = Math.max(1, parseFloat(document.getElementById('combo_picker_qty').value) || 1);

        var emptyRow = document.getElementById('emptyComboRow');
        if (emptyRow) emptyRow.remove();

        var existingRow = document.getElementById('combo_comp_row_' + compId);
        if (existingRow) existingRow.remove();

        var stockBadge = (type === 'SERVICIO')
            ? '<span class="badge badge-info">🍽️ Plato Cocina (Sin límite de stock)</span>'
            : '<span class="badge badge-success">📦 Descuenta ' + qty + ' u. del Kardex al vender</span>';

        var tr = document.createElement('tr');
        tr.id = 'combo_comp_row_' + compId;
        tr.setAttribute('data-cost', cost);
        tr.setAttribute('data-price', price);
        tr.setAttribute('data-qty', qty);
        tr.innerHTML =
            '<td class="font-weight-bold">' + name + '<input type="hidden" name="combo_component_id[]" value="' + compId + '"></td>' +
            '<td>' + stockBadge + '</td>' +
            '<td class="text-center font-weight-bold">' + qty + '<input type="hidden" name="combo_component_qty[]" value="' + qty + '"></td>' +
            '<td class="text-right">{{ $sym }} ' + (cost * qty).toFixed(2) + '</td>' +
            '<td class="text-right text-muted">{{ $sym }} ' + (price * qty).toFixed(2) + '</td>' +
            '<td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeComboRow(\'' + compId + '\')"><i class="fas fa-trash-alt"></i></button></td>';

        document.getElementById('comboComponentsBody').appendChild(tr);
        sel.value = '';
        document.getElementById('combo_picker_qty').value = 1;
        recalcComboFinancials();
    }

    function removeComboRow(compId) {
        var r = document.getElementById('combo_comp_row_' + compId);
        if (r) r.remove();
        recalcComboFinancials();
    }

    function recalcComboFinancials() {
        var totalCost = 0;
        var totalNormalPrice = 0;
        document.querySelectorAll('#comboComponentsBody tr[data-cost]').forEach(function(tr) {
            var c = parseFloat(tr.getAttribute('data-cost')) || 0;
            var p = parseFloat(tr.getAttribute('data-price')) || 0;
            var q = parseFloat(tr.getAttribute('data-qty')) || 1;
            totalCost += (c * q);
            totalNormalPrice += (p * q);
        });

        document.getElementById('purchase_price').value = totalCost.toFixed(2);
        var summary = document.getElementById('comboFinancialSummary');
        if (summary) {
            summary.innerText = 'Costo Total Receta: {{ $sym }} ' + totalCost.toFixed(2) + ' | Suma Precios Individuales: {{ $sym }} ' + totalNormalPrice.toFixed(2);
        }
        calcMargin();
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleServiceFields();
    });
</script>
@endsection
