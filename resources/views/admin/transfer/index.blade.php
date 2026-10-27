@extends('layouts.admin')
@section('title', 'Traspasos de Mercadería entre Sucursales')

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
@endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h3 class="page-title font-weight-bold mb-1">
                <i class="fas fa-truck-loading text-primary mr-2"></i>Traspasos de Mercadería entre Sucursales
            </h3>
            <p class="text-muted mb-0 small">
                Cada sucursal maneja su propio stock independiente. Transfiere productos de una sucursal o almacén a otra con Guía de Remisión en PDF y registro en Kardex.
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('company.index') }}" class="btn btn-outline-primary btn-sm mr-1">
                <i class="fas fa-store-alt mr-1"></i> Gestionar Sucursales ({{ $branches->count() }})
            </a>
            <a href="{{ route('kardex.index') }}" class="btn btn-outline-warning text-dark font-weight-bold btn-sm">
                <i class="fas fa-clipboard-list mr-1"></i> Ver Kardex por Sucursal
            </a>
        </div>
    </div>

    @if($branches->count() < 2)
        <div class="alert alert-warning border-left-warning shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle mr-2 fa-lg"></i>
                <strong>Atención:</strong> Actualmente tienes <strong>{{ $branches->count() }} sucursal</strong> registrada ({{ $branches->first()->name ?? 'Casa Matriz' }}). Para realizar traspasos entre sucursales, crea al menos una segunda sucursal o almacén.
            </div>
            <a href="{{ route('company.index') }}" class="btn btn-warning text-dark font-weight-bold btn-sm">
                <i class="fas fa-plus-circle mr-1"></i> Crear Nueva Sucursal Ahora
            </a>
        </div>
    @endif

    <div class="row">
        <!-- COLUMNA IZQUIERDA: FORMULARIO DE NUEVO TRASPASO -->
        <div class="col-lg-5 grid-margin stretch-card">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary font-weight-bold mb-3">
                        <i class="fas fa-exchange-alt mr-1"></i> Nuevo Traspaso entre Sucursales
                    </h5>

                    <form action="{{ route('transfers.store') }}" method="POST" id="transferForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-danger">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Sucursal Origen (Sale) *
                                </label>
                                <select name="from_branch_id" id="from_branch_id" class="form-control font-weight-bold" onchange="updateOriginStockDisplay()" required>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ (isset($activeBranch) && $activeBranch->id == $b->id) ? 'selected' : '' }}>
                                            {{ $b->name }} ({{ $b->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-success">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Sucursal Destino (Entra) *
                                </label>
                                <select name="to_branch_id" id="to_branch_id" class="form-control font-weight-bold" onchange="updateOriginStockDisplay()" required>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ (isset($activeBranch) && $activeBranch->id != $b->id) ? 'selected' : '' }}>
                                            {{ $b->name }} ({{ $b->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="p-3 rounded mb-3 border" style="background:#f8fafc;">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">Seleccionar Producto a Traspasar</label>
                                <select id="select_product" class="form-control js-example-basic-single w-100" onchange="updateOriginStockDisplay()">
                                    <option value="">-- Seleccione un producto --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-code="{{ $p->code }}" data-unit="{{ $p->unit_measure ?? 'UND' }}">
                                            [{{ $p->code }}] {{ $p->name }} {{ $p->sku ? '('.$p->sku.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2 small">
                                <span>Stock en Origen: <strong id="lbl_origin_stock" class="badge badge-info px-2">0</strong></span>
                                <span>Stock en Destino: <strong id="lbl_dest_stock" class="badge badge-secondary px-2">0</strong></span>
                            </div>

                            <div class="row align-items-end">
                                <div class="col-7">
                                    <label class="font-weight-bold small mb-1">Cantidad a Enviar</label>
                                    <input type="number" id="input_qty" class="form-control form-control-sm" value="1" min="1" step="1">
                                </div>
                                <div class="col-5">
                                    <button type="button" class="btn btn-info btn-sm btn-block font-weight-bold" onclick="addTransferItem()">
                                        <i class="fas fa-plus mr-1"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TABLA DE ÍTEMS A TRASPASAR -->
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center" style="width:90px;">Cant.</th>
                                        <th style="width:40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="transferItemsBody">
                                    <tr id="emptyTransferRow">
                                        <td colspan="3" class="text-center text-muted py-3 small">
                                            Agregue productos a la lista de traspaso...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small">Motivo / Responsable de Transporte / Observaciones</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Ej: Reposición semanal de stock - Enviado con Chofer Juan...">
                        </div>

                        <button type="submit" id="btnSubmitTransfer" class="btn btn-primary btn-block font-weight-bold" disabled>
                            <i class="fas fa-paper-plane mr-1"></i> Ejecutar Traspaso y Generar Guía
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: HISTORIAL DE TRASPASOS Y STOCK POR SUCURSAL -->
        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title font-weight-bold mb-3">
                        <i class="fas fa-history text-primary mr-2"></i>Historial de Traspasos entre Sucursales
                    </h5>

                    <div class="table-responsive">
                        <table id="order-listing" class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Guía / Fecha</th>
                                    <th>Origen <i class="fas fa-arrow-right mx-1"></i> Destino</th>
                                    <th>Ítems Enviados</th>
                                    <th>Estado</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfers as $tr)
                                <tr>
                                    <td>
                                        <span class="badge badge-dark font-weight-bold">{{ $tr->reference_code }}</span><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($tr->transfer_date)->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="text-danger font-weight-bold"><i class="fas fa-store-alt mr-1"></i>{{ $tr->fromBranch->name ?? 'Origen' }}</span>
                                        <br>
                                        <span class="text-success font-weight-bold"><i class="fas fa-arrow-down mr-1"></i>{{ $tr->toBranch->name ?? 'Destino' }}</span>
                                    </td>
                                    <td>
                                        @foreach($tr->details as $det)
                                            <div class="small">
                                                <strong>{{ number_format($det->quantity, 0) }} {{ $det->product->unit_measure ?? 'UND' }}</strong> — {{ $det->product->name ?? 'Ítem' }}
                                            </div>
                                        @endforeach
                                        @if($tr->notes)
                                            <small class="text-muted d-block mt-1"><em>"{{ $tr->notes }}"</em></small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tr->status === 'COMPLETADO')
                                            <span class="badge badge-success">COMPLETADO</span>
                                        @else
                                            <span class="badge badge-danger">ANULADO</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('transfers.pdf', $tr) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Imprimir Guía de Remisión / Traspaso PDF">
                                            <i class="fas fa-file-pdf"></i> Guía PDF
                                        </a>
                                        @if($tr->status === 'COMPLETADO')
                                            <form action="{{ route('transfers.cancel', $tr) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Anular este traspaso y devolver el stock a la sucursal de origen?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Anular y Revertir Stock">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Aún no se han registrado traspasos entre sucursales.
                                    </td>
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

@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
{!! Html::script('melody/js/select2.js') !!}
<script>
    var branchStockMap = @json($branchStockMap);
    var transferCart = {};

    function updateOriginStockDisplay() {
        var prodSelect = document.getElementById('select_product');
        var prodId = prodSelect.value;
        var fromId = document.getElementById('from_branch_id').value;
        var toId = document.getElementById('to_branch_id').value;

        if (!prodId || !branchStockMap[prodId]) {
            document.getElementById('lbl_origin_stock').innerText = '0';
            document.getElementById('lbl_dest_stock').innerText = '0';
            return;
        }

        var originStock = branchStockMap[prodId][fromId] || 0;
        var destStock = branchStockMap[prodId][toId] || 0;

        document.getElementById('lbl_origin_stock').innerText = originStock;
        document.getElementById('lbl_dest_stock').innerText = destStock;
        document.getElementById('input_qty').max = originStock;
    }

    function addTransferItem() {
        var fromId = document.getElementById('from_branch_id').value;
        var toId = document.getElementById('to_branch_id').value;
        if (fromId === toId) {
            alert('La sucursal de destino debe ser diferente a la sucursal de origen.');
            return;
        }

        var prodSelect = document.getElementById('select_product');
        var prodId = prodSelect.value;
        if (!prodId) {
            alert('Seleccione un producto.');
            return;
        }

        var opt = prodSelect.options[prodSelect.selectedIndex];
        var prodName = opt.getAttribute('data-name');
        var prodUnit = opt.getAttribute('data-unit');
        var qty = parseInt(document.getElementById('input_qty').value) || 0;
        var available = (branchStockMap[prodId] && branchStockMap[prodId][fromId]) ? branchStockMap[prodId][fromId] : 0;

        if (qty <= 0) {
            alert('Ingrese una cantidad válida mayor a 0.');
            return;
        }

        var currentInCart = transferCart[prodId] ? transferCart[prodId].qty : 0;
        if ((currentInCart + qty) > available) {
            alert('Stock insuficiente en la sucursal de origen. Disponible: ' + available);
            return;
        }

        transferCart[prodId] = {
            id: prodId,
            name: prodName,
            unit: prodUnit,
            qty: currentInCart + qty
        };

        renderTransferCart();
    }

    function removeTransferItem(prodId) {
        delete transferCart[prodId];
        renderTransferCart();
    }

    function renderTransferCart() {
        var tbody = document.getElementById('transferItemsBody');
        tbody.innerHTML = '';
        var keys = Object.keys(transferCart);

        if (keys.length === 0) {
            tbody.innerHTML = '<tr id="emptyTransferRow"><td colspan="3" class="text-center text-muted py-3 small">Agregue productos a la lista de traspaso...</td></tr>';
            document.getElementById('btnSubmitTransfer').disabled = true;
            return;
        }

        keys.forEach(function(k) {
            var item = transferCart[k];
            var tr = document.createElement('tr');
            tr.innerHTML = '<td><input type="hidden" name="product_id[]" value="' + item.id + '"><strong>' + item.name + '</strong></td>' +
                           '<td class="text-center"><input type="hidden" name="quantity[]" value="' + item.qty + '"><span class="badge badge-primary px-2 py-1">' + item.qty + ' ' + item.unit + '</span></td>' +
                           '<td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTransferItem(' + item.id + ')"><i class="fas fa-times"></i></button></td>';
            tbody.appendChild(tr);
        });

        document.getElementById('btnSubmitTransfer').disabled = false;
    }
</script>
@endsection

