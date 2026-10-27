@extends('layouts.admin')
@section('title', 'Comanda ' . $order->order_number)

@section('styles')
<style>
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        max-height: 540px;
        overflow-y: auto;
    }
    .dish-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px;
        background: #fff;
        cursor: pointer;
        transition: all 0.15s ease;
        position: relative;
    }
    .dish-card:hover {
        border-color: #4f46e5;
        background: #eef2ff;
        transform: translateY(-2px);
    }
    .cat-btn {
        border-radius: 20px;
        margin-right: 6px;
        margin-bottom: 6px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
@endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <span class="badge badge-danger font-weight-bold px-2 py-1 mb-1">
                @if($order->service_type === 'MESA')
                    <i class="fas fa-chair mr-1"></i> {{ $order->table->name ?? 'Mesa' }} ({{ $order->table->zone ?? 'Salón' }})
                @else
                    <i class="fas fa-motorcycle mr-1"></i> {{ $order->service_type }}: {{ $order->customer_name }}
                @endif
            </span>
            <h3 class="page-title font-weight-bold mb-0">
                Comanda {{ $order->order_number }} — Consumo Actual: <span class="text-primary">{{ $sym }} {{ number_format($order->total, 2) }}</span>
            </h3>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('restaurant.orders.kitchen', $order) }}" target="_blank" class="btn btn-dark font-weight-bold mr-1">
                <i class="fas fa-fire-alt text-warning mr-1"></i> Imprimir Comanda Cocina (80mm)
            </a>
            <a href="{{ route('restaurant.orders.prebill', $order) }}" target="_blank" class="btn btn-warning text-dark font-weight-bold mr-1">
                <i class="fas fa-receipt mr-1"></i> Imprimir Pre-Cuenta Mesa (80mm)
            </a>
            <a href="{{ route('restaurant.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Mapa de Mesas
            </a>
        </div>
    </div>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: CARTA DIGITAL / PLATOS Y BEBIDAS -->
        <div class="col-lg-7 grid-margin">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0 font-weight-bold">
                            <i class="fas fa-utensils text-primary mr-1"></i> Carta / Menú del Restaurante
                        </h5>
                        <input type="text" id="searchDish" class="form-control form-control-sm" style="max-width:220px;" placeholder="Buscar plato o bebida..." onkeyup="filterMenu()">
                    </div>

                    <!-- FILTROS DE CATEGORÍA -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-primary cat-btn" onclick="filterByCategory('ALL', this)">Todos</button>
                        @foreach($categories as $cat)
                            <button type="button" class="btn btn-sm btn-outline-primary cat-btn" onclick="filterByCategory('{{ $cat->id }}', this)">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- FORMULARIO RÁPIDO PARA ENVIAR PLATO A LA COMANDA CON NOTA DE COCINA -->
                    <form action="{{ route('restaurant.orders.items.add', $order) }}" method="POST" class="p-3 rounded border mb-3" style="background:#f8fafc;">
                        @csrf
                        <input type="hidden" name="product_id" id="selected_product_id" required>
                        <div class="row align-items-end">
                            <div class="col-md-5 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold text-primary mb-1">Plato / Bebida Seleccionado:</label>
                                <input type="text" id="selected_product_name" class="form-control form-control-sm font-weight-bold bg-white" placeholder="Haga clic en un plato abajo..." readonly required>
                            </div>
                            <div class="col-md-2 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold mb-1">Cant.</label>
                                <input type="number" name="quantity" id="selected_qty" class="form-control form-control-sm text-center font-weight-bold" value="1" min="1" step="1" required>
                            </div>
                            <div class="col-md-3 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold mb-1">Nota para Cocina / Barra:</label>
                                <input type="text" name="notes" id="selected_notes" class="form-control form-control-sm" placeholder="Ej: Sin cebolla / Bien cocido">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" id="btnAddDish" class="btn btn-success btn-sm btn-block font-weight-bold" disabled>
                                    <i class="fas fa-plus"></i> Comandar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- GRID DE PLATOS Y BEBIDAS -->
                    <div class="menu-grid" id="menuGrid">
                        @foreach($products as $prod)
                            <div class="dish-card"
                                 data-category="{{ $prod->category_id }}"
                                 data-name="{{ strtolower($prod->name) }}"
                                 onclick="selectDish({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->sell_price }})">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="badge badge-dark">{{ $sym }} {{ number_format($prod->sell_price, 2) }}</span>
                                    @if(!$prod->isService())
                                        <span class="badge badge-outline-success" style="font-size:10px;">Stock: {{ $prod->stock }}</span>
                                    @endif
                                </div>
                                <div class="font-weight-bold text-dark mt-1" style="font-size:13px; line-height:1.25;">
                                    {{ $prod->name }}
                                </div>
                                <small class="text-muted d-block mt-1">{{ $prod->category->name ?? '' }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: DETALLE DE CONSUMO DE LA MESA Y CIERRE / COBRO DE CUENTA -->
        <div class="col-lg-5 grid-margin">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0 font-weight-bold">
                            <i class="fas fa-receipt text-primary mr-1"></i> Consumo de la Mesa
                        </h5>
                        <span class="badge badge-info">{{ $order->items->sum('quantity') }} ítems</span>
                    </div>

                    <div class="table-responsive mb-3" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Cant / Plato</th>
                                    <th>Cocina</th>
                                    <th class="text-right">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ number_format($item->quantity, 0) }}x {{ $item->product->name ?? 'Ítem' }}</strong>
                                            @if($item->notes)
                                                <br><small class="text-danger font-weight-bold"><i class="fas fa-comment-dots"></i> {{ $item->notes }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->kitchen_status === 'EN_COCINA')
                                                <form action="{{ route('restaurant.items.status', $item) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="kitchen_status" value="SERVIDO">
                                                    <button type="submit" class="badge badge-warning text-dark border-0" title="Clic para marcar como Servido">
                                                        🔥 Cocina
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge badge-success">✅ Servido</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            {{ $sym }} {{ number_format($item->quantity * $item->price, 2) }}
                                        </td>
                                        <td class="text-right">
                                            <form action="{{ route('restaurant.items.remove', $item) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Seleccione platos de la carta izquierda para enviarlos a la comanda...
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- FORMULARIO DE COBRO Y FACTURACIÓN DE LA MESA -->
                    <form action="{{ route('restaurant.orders.checkout', $order) }}" method="POST" class="p-3 rounded border" style="background:#f8fafc;">
                        @csrf
                        <h6 class="font-weight-bold text-success border-bottom pb-2 mb-2">
                            <i class="fas fa-cash-register mr-1"></i> Cobrar Cuenta y Liberar Mesa
                        </h6>

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold mb-1">Cliente para el Comprobante / Factura:</label>
                            <select name="client_id" class="form-control form-control-sm js-example-basic-single w-100">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $order->client_id == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} (NIT/CI: {{ $client->ci ?: $client->nit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6 form-group mb-2">
                                <label class="small font-weight-bold mb-1">Descuento ({{ $sym }}):</label>
                                <input type="number" step="0.5" min="0" name="discount_amount" id="chk_discount" class="form-control form-control-sm" value="{{ $order->discount ?? 0 }}" oninput="recalcCheckout()">
                            </div>
                            <div class="col-6 form-group mb-2">
                                <label class="small font-weight-bold mb-1">Propina / Servicio ({{ $sym }}):</label>
                                <input type="number" step="0.5" min="0" name="tip_amount" id="chk_tip" class="form-control form-control-sm" value="{{ $order->tip_amount ?? 0 }}" oninput="recalcCheckout()">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-7 form-group mb-2">
                                <label class="small font-weight-bold mb-1">Forma de Pago:</label>
                                <select name="payment_method_id" class="form-control form-control-sm font-weight-bold">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5 form-group mb-2">
                                <label class="small font-weight-bold mb-1">Efectivo Recibido:</label>
                                <input type="number" step="0.5" name="cash_received" id="chk_received" class="form-control form-control-sm" value="{{ $order->total }}" oninput="recalcCheckout()">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded border mb-2">
                            <div>
                                <span class="small text-muted d-block">Total a Cobrar:</span>
                                <strong class="h4 mb-0 text-primary" id="lbl_final_total">{{ $sym }} {{ number_format($order->total, 2) }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="small text-muted d-block">Cambio / Vuelto:</span>
                                <strong class="h5 mb-0 text-success" id="lbl_final_change">{{ $sym }} 0.00</strong>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-block font-weight-bold" {{ $order->items->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-check-circle mr-1"></i> COBRAR CUENTA, EMITIR TICKET Y LIBERAR MESA
                        </button>
                    </form>

                    @if($order->items->isEmpty())
                        <form action="{{ route('restaurant.orders.cancel', $order) }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm btn-block">
                                <i class="fas fa-times mr-1"></i> Desocupar / Cancelar Apertura de Mesa
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! Html::script('melody/js/select2.js') !!}
<script>
    var baseSubtotal = {{ floatval($order->subtotal) }};
    var sym = "{{ $sym }}";

    function selectDish(id, name, price) {
        document.getElementById('selected_product_id').value = id;
        document.getElementById('selected_product_name').value = name + ' (' + sym + ' ' + price.toFixed(2) + ')';
        document.getElementById('btnAddDish').disabled = false;
        document.getElementById('selected_notes').focus();
    }

    function filterByCategory(catId, btn) {
        document.querySelectorAll('.cat-btn').forEach(function(b) {
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline-primary');
        });
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');

        document.querySelectorAll('.dish-card').forEach(function(card) {
            if (catId === 'ALL' || card.getAttribute('data-category') === catId) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterMenu() {
        var q = document.getElementById('searchDish').value.toLowerCase();
        document.querySelectorAll('.dish-card').forEach(function(card) {
            var name = card.getAttribute('data-name') || '';
            card.style.display = name.indexOf(q) !== -1 ? 'block' : 'none';
        });
    }

    function recalcCheckout() {
        var disc = parseFloat(document.getElementById('chk_discount').value) || 0;
        var tip = parseFloat(document.getElementById('chk_tip').value) || 0;
        var rec = parseFloat(document.getElementById('chk_received').value) || 0;

        var finalTotal = Math.max(0, baseSubtotal - disc + tip);
        var change = Math.max(0, rec - finalTotal);

        document.getElementById('lbl_final_total').innerText = sym + ' ' + finalTotal.toFixed(2);
        document.getElementById('lbl_final_change').innerText = sym + ' ' + change.toFixed(2);
    }
</script>
@endsection
