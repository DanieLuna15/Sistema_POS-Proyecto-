@extends('layouts.admin')
@section('title', 'Comanda ' . $order->order_number)

@section('styles')
<style>
    /* Píldoras de Categoría idénticas a Caja Rápida (POS) */
    .category-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }
    .category-pill {
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid #d1d5db;
        background: #f8fafc;
        color: #374151;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .category-pill.active, .category-pill:hover {
        background: #4b49ac;
        color: #fff;
        border-color: #4b49ac;
    }

    /* Cuadrícula y Tarjetas de Productos idénticas a Caja Rápida (POS) */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
        gap: 12px;
        max-height: 560px;
        overflow-y: auto;
        padding: 4px;
    }
    .product-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
        background: #fff;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        user-select: none;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(75, 73, 172, 0.15);
        border-color: #4b49ac;
    }
    .product-card.selected-dish {
        border-color: #10b981;
        background: #f0fdf4;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
    }
    .product-img-box {
        height: 85px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 6px;
        background: #f8fafc;
        border-radius: 8px;
        overflow: hidden;
    }
    .product-img-box img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    .product-price-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #1e1b4b;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 12px;
        z-index: 2;
    }

    /* Botones + / - estilo Caja Rápida */
    .qty-btn {
        width: 30px;
        height: 30px;
        padding: 0;
        border-radius: 6px;
        border: 1px solid #4b49ac;
        background: #eef2ff;
        color: #4b49ac;
        font-weight: 900;
        font-size: 16px;
        line-height: 26px;
        text-align: center;
        cursor: pointer;
        flex-shrink: 0;
    }
    .qty-btn:hover {
        background: #4b49ac;
        color: #fff;
    }

    /* Ítems en la lista de la comanda derecha */
    .ticket-items {
        min-height: 200px;
        max-height: 320px;
        overflow-y: auto;
        margin-top: 8px;
        margin-bottom: 12px;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        padding: 8px;
    }
    .ticket-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        margin-bottom: 6px;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        gap: 6px;
    }
    .btn-checkout-responsive {
        white-space: normal !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        line-height: 1.35 !important;
        font-size: 13.5px !important;
        padding: 11px 12px !important;
        height: auto !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 6px;
    }
</style>
@endsection

@section('content')
@php
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
@endphp
<div class="content-wrapper">
    <!-- CABECERA DE LA COMANDA -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div>
            <span class="badge badge-danger font-weight-bold px-3 py-1 mb-1">
                @if($order->service_type === 'MESA')
                    <i class="fas fa-chair mr-1"></i> {{ $order->table->name ?? 'Mesa' }} — Área: {{ $order->table->zone ?? 'Salón Principal' }}
                @else
                    <i class="fas fa-motorcycle mr-1"></i> {{ $order->service_type }}: {{ $order->customer_name }}
                @endif
            </span>
            <h3 class="page-title font-weight-bold mb-0">
                Comanda {{ $order->order_number }} &bull; Consumo: <span class="text-primary" id="header_order_total">{{ $sym }} {{ number_format($order->total, 2) }}</span>
            </h3>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap" style="gap: 6px;">
            <a href="{{ route('restaurant.kitchen') }}" class="btn btn-outline-dark btn-sm font-weight-bold">
                <i class="fas fa-tv text-warning mr-1"></i> Pantalla Cocina (KDS)
            </a>
            <a href="{{ route('restaurant.orders.kitchen', $order) }}" target="_blank" class="btn btn-dark btn-sm font-weight-bold">
                <i class="fas fa-fire-alt text-warning mr-1"></i> Ticket Cocina (80mm)
            </a>
            <a href="{{ route('restaurant.orders.prebill', $order) }}" target="_blank" class="btn btn-warning text-dark btn-sm font-weight-bold">
                <i class="fas fa-receipt mr-1"></i> Pre-Cuenta (80mm)
            </a>
            <a href="{{ route('restaurant.index') }}" class="btn btn-secondary btn-sm font-weight-bold">
                <i class="fas fa-map-marked-alt mr-1"></i> Volver al Mapa
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ========================================================================= -->
        <!-- COLUMNA IZQUIERDA (7 COLUMNAS): CATÁLOGO VISUAL IDÉNTICO A CAJA RÁPIDA    -->
        <!-- ========================================================================= -->
        <div class="col-lg-7 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <!-- BUSCADOR RÁPIDO -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-primary"></i>
                            </span>
                        </div>
                        <input type="text" id="searchDish" class="form-control"
                               placeholder="Buscar plato, bebida o código... (Clic repetido en un plato suma +1 a la cantidad)"
                               onkeyup="filterMenu()">
                    </div>

                    <!-- CATEGORÍAS ESTILO PILLS (IDÉNTICO A CAJA RÁPIDA) -->
                    <div class="category-pills">
                        <button type="button" class="category-pill active" onclick="filterByCategory('ALL', this)">
                            <i class="fas fa-th-large mr-1"></i> Todos ({{ $products->count() }})
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" class="category-pill" onclick="filterByCategory('{{ $cat->id }}', this)">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- BARRA DE PRE-SELECCIÓN CON BOTONES - / + (CADA CLIC EN EL PLATO INCREMENTA LA CANTIDAD) -->
                    <form id="addDishForm" action="{{ route('restaurant.orders.items.add', $order) }}" method="POST"
                          onsubmit="return submitAddDishAjax(event)"
                          class="p-2 px-3 rounded border mb-3" style="background:#eef2ff; border-color:#c7d2fe !important;">
                        @csrf
                        <input type="hidden" name="product_id" id="selected_product_id" required>
                        <div class="row align-items-end">
                            <div class="col-md-4 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold text-primary mb-1">
                                    <i class="fas fa-utensils mr-1"></i> Plato / Bebida Seleccionado:
                                </label>
                                <input type="text" id="selected_product_name" class="form-control form-control-sm font-weight-bold bg-white"
                                       placeholder="Haga clic en un plato abajo..." readonly required>
                            </div>

                            <!-- CONTROL DE CANTIDAD CON BOTONES - Y + COMO EN CAJA RÁPIDA -->
                            <div class="col-md-3 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold text-dark mb-1 d-block text-center">Cantidad a Comandar:</label>
                                <div class="d-flex align-items-center justify-content-center">
                                    <button type="button" class="qty-btn mr-1" onclick="changeSelectedQty(-1)" title="Reducir cantidad">-</button>
                                    <input type="number" name="quantity" id="selected_qty"
                                           class="form-control form-control-sm text-center font-weight-bold px-1"
                                           style="width: 55px; height: 30px;" value="1" min="1" step="1" required
                                           oninput="syncBadgeWithQty()">
                                    <button type="button" class="qty-btn ml-1" onclick="changeSelectedQty(1)" title="Aumentar cantidad">+</button>
                                </div>
                            </div>

                            <div class="col-md-3 form-group mb-2 mb-md-0">
                                <label class="small font-weight-bold mb-1">Nota Cocina (Opcional):</label>
                                <input type="text" name="notes" id="selected_notes" class="form-control form-control-sm"
                                       placeholder="Ej: Sin cebolla / Helada">
                            </div>

                            <div class="col-md-2">
                                <button type="submit" id="btnAddDish" class="btn btn-primary btn-sm btn-block font-weight-bold py-2 shadow-sm" disabled>
                                    <i class="fas fa-plus-circle mr-1"></i> Comandar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- GRID VISUAL DE PLATOS Y BEBIDAS (DISEÑO CAJA RÁPIDA POS) -->
                    <div class="product-grid" id="menuGrid">
                        @forelse($products as $prod)
                            @php
                                $isUnlimited = $prod->isService() || $prod->item_type === 'COMBO' || $prod->stock <= 0;
                            @endphp
                            <div class="product-card"
                                 id="dish_card_{{ $prod->id }}"
                                 data-id="{{ $prod->id }}"
                                 data-category="{{ $prod->category_id }}"
                                 data-name="{{ strtolower($prod->name . ' ' . ($prod->code ?? '')) }}"
                                 data-title="{{ $prod->name }}"
                                 data-price="{{ $prod->sell_price }}"
                                 onclick="selectOrIncrementDish(this)">

                                <span class="product-price-badge">{{ $sym }} {{ number_format($prod->sell_price, 2) }}</span>
                                <span class="badge badge-danger position-absolute" id="dish_select_badge_{{ $prod->id }}"
                                      style="top:8px; left:8px; display:none; font-size:11px; padding:4px 7px; border-radius:10px; z-index:2;">
                                    1x
                                </span>

                                <div class="product-img-box">
                                    @if($prod->image && file_exists(public_path('image/' . $prod->image)))
                                        <img src="{{ asset('image/' . $prod->image) }}" alt="{{ $prod->name }}">
                                    @else
                                        <i class="fas {{ $prod->item_type === 'COMBO' ? 'fa-gifts text-warning' : ($isUnlimited ? 'fa-utensils text-primary' : 'fa-wine-bottle text-info') }} fa-2x"></i>
                                    @endif
                                </div>

                                <div>
                                    <div class="font-weight-bold text-dark text-truncate" style="font-size:13px;" title="{{ $prod->name }}">
                                        {{ $prod->name }}
                                    </div>
                                    <small class="text-muted d-block text-truncate" style="font-size:11px;">
                                        {{ $prod->category->name ?? ($prod->brand->name ?? 'General') }}
                                    </small>

                                    @if($prod->item_type === 'COMBO')
                                        <span class="badge badge-warning text-dark mt-1" style="font-size:10px;">
                                            <i class="fas fa-layer-group"></i> Combo Promocional
                                        </span>
                                    @elseif(!$prod->isService() && $prod->stock > 0)
                                        <span class="badge badge-success mt-1" style="font-size:10px;">Stock: {{ $prod->stock }} {{ $prod->unit_measure ?? 'UND' }}</span>
                                    @else
                                        <span class="badge badge-info mt-1" style="font-size:10px;">Plato / Cocina</span>
                                    @endif

                                    <div class="mt-2 d-flex" style="gap: 4px;">
                                        <span class="btn btn-xs btn-outline-primary flex-grow-1 font-weight-bold py-1" title="Cada clic suma +1 arriba">
                                            <i class="fas fa-hand-pointer"></i> Elegir
                                        </span>
                                        <button type="button" class="btn btn-xs btn-primary font-weight-bold py-1 px-2"
                                                onclick="event.stopPropagation(); quickAddFromCard(this)"
                                                title="Enviar +1 directo a la comanda por AJAX">
                                            +1 Directo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-5">
                                <i class="fas fa-utensils fa-3x mb-2 d-block text-secondary"></i>
                                <h6>No hay platos registrados aún.</h6>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- COLUMNA DERECHA (5 COLUMNAS): COMANDA DE LA MESA Y COBRO                  -->
        <!-- ========================================================================= -->
        <div class="col-lg-5 mb-3">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #4b49ac !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="font-weight-bold mb-0 text-dark">
                            <i class="fas fa-receipt text-primary mr-1"></i> Platos Comandados en la Mesa
                        </h6>
                        <span class="badge badge-primary px-2 py-1" id="badge_total_items">{{ number_format($order->items->sum('quantity'), 0) }} ítems</span>
                    </div>

                    <!-- LISTA DE PLATOS COMANDADOS CON CONTROLES - / + Y ESTADO AJAX -->
                    <div class="ticket-items" id="orderItemsContainer">
                        @forelse($order->items as $item)
                            <div class="ticket-item" id="order_item_row_{{ $item->id }}">
                                <div style="flex: 1; min-width: 0;" class="pr-1">
                                    <div class="font-weight-bold text-dark text-truncate" style="font-size:13px;" title="{{ $item->product->name ?? 'Plato' }}">
                                        {{ $item->product->name ?? 'Plato' }}
                                    </div>
                                    @if($item->notes)
                                        <small class="text-danger font-weight-bold d-block">
                                            <i class="fas fa-comment-dots"></i> {{ $item->notes }}
                                        </small>
                                    @endif
                                    <div class="d-flex align-items-center mt-1" style="gap: 6px;">
                                        <small class="text-muted">{{ $sym }} {{ number_format($item->price, 2) }} c/u</small>
                                        <!-- Estado en Cocina cambiable por AJAX sin recargar -->
                                        <button type="button"
                                                id="item_status_btn_{{ $item->id }}"
                                                data-status="{{ $item->kitchen_status }}"
                                                onclick="toggleOrderItemStatusAjax({{ $item->id }})"
                                                class="badge border-0 py-1 px-2 {{ $item->kitchen_status === 'EN_COCINA' ? 'badge-warning text-dark' : ($item->kitchen_status === 'LISTO' ? 'badge-primary' : 'badge-success') }}"
                                                style="cursor:pointer; font-size:10px;">
                                            @if($item->kitchen_status === 'EN_COCINA')
                                                🔥 Cocina
                                            @elseif($item->kitchen_status === 'LISTO')
                                                🔔 ¡Listo!
                                            @else
                                                ✅ Servido
                                            @endif
                                        </button>
                                    </div>
                                </div>

                                <!-- BOTONES - / CANTIDAD / + COMO EN CAJA RÁPIDA -->
                                <div class="d-flex align-items-center">
                                    <button type="button" class="qty-btn mr-1" onclick="updateOrderedItemQtyAjax({{ $item->id }}, -1)">-</button>
                                    <input type="number" min="1" id="ordered_qty_input_{{ $item->id }}"
                                           value="{{ intval($item->quantity) }}"
                                           onchange="setOrderedItemQtyAjax({{ $item->id }}, this.value)"
                                           class="form-control form-control-sm text-center font-weight-bold px-1"
                                           style="width: 44px; height: 30px;">
                                    <button type="button" class="qty-btn ml-1" onclick="updateOrderedItemQtyAjax({{ $item->id }}, 1)">+</button>
                                </div>

                                <div class="d-flex align-items-center justify-content-end" style="gap: 8px; min-width: 105px;">
                                    <strong class="text-primary" style="font-size:13px;" id="ordered_line_sub_{{ $item->id }}">
                                        {{ $sym }} {{ number_format($item->quantity * $item->price, 2) }}
                                    </strong>
                                    <button type="button" class="btn btn-xs btn-outline-danger p-1 px-2"
                                            onclick="removeOrderedItemAjax({{ $item->id }})"
                                            title="Quitar de la comanda">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div id="emptyOrderMsg" class="text-center text-muted py-4">
                                <i class="fas fa-concierge-bell fa-2x mb-2 text-secondary d-block"></i>
                                <strong class="small d-block">La comanda está vacía</strong>
                                <small>Haga clic en un plato de la izquierda para elegir cantidad o pulse <strong>+1 Directo</strong>.</small>
                            </div>
                        @endforelse
                    </div>

                    <!-- FORMULARIO DE COBRO Y LIBERACIÓN DE MESA -->
                    <form action="{{ route('restaurant.orders.checkout', $order) }}" method="POST" class="p-3 rounded border" style="background:#f8fafc;">
                        @csrf
                        <h6 class="font-weight-bold text-success border-bottom pb-2 mb-2">
                            <i class="fas fa-cash-register mr-1"></i> Cerrar y Cobrar Cuenta de la Mesa
                        </h6>

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold mb-1">Cliente para el Comprobante / Recibo:</label>
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
                                <input type="number" step="0.5" name="cash_received" id="chk_received" class="form-control form-control-sm font-weight-bold" value="{{ $order->total }}" oninput="recalcCheckout()">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-2 rounded mb-2" style="background:#eef2ff; border:1px solid #c7d2fe;">
                            <div>
                                <span class="small text-muted d-block">Total a Cobrar:</span>
                                <strong class="h4 mb-0 text-primary" id="lbl_final_total">{{ $sym }} {{ number_format($order->total, 2) }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="small text-muted d-block">Cambio / Vuelto:</span>
                                <strong class="h5 mb-0 text-success" id="lbl_final_change">{{ $sym }} 0.00</strong>
                            </div>
                        </div>

                        <button type="submit" id="btnCheckoutOrder" class="btn btn-success btn-block font-weight-bold shadow btn-checkout-responsive" {{ $order->items->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-check-circle"></i>
                            <span>COBRAR CUENTA, EMITIR TICKET Y LIBERAR MESA</span>
                        </button>
                    </form>

                    <form action="{{ route('restaurant.orders.cancel', $order) }}" method="POST" class="mt-2" id="cancelTableForm" style="{{ $order->items->isEmpty() ? '' : 'display:none;' }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm btn-block">
                            <i class="fas fa-times mr-1"></i> Desocupar / Cancelar Apertura de Mesa
                        </button>
                    </form>
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
    var csrfToken = "{{ csrf_token() }}";
    var currentSelectedDishId = null;

    /**
     * Si el usuario hace 1 clic en un plato: lo selecciona con cantidad 1.
     * Si vuelve a hacer clic (2 clics, 3 clics...) en el mismo plato: ¡incrementa la cantidad automáticamente (2, 3, 4...)!
     */
    function selectOrIncrementDish(cardElem) {
        var id = cardElem.getAttribute('data-id');
        var title = cardElem.getAttribute('data-title');
        var price = parseFloat(cardElem.getAttribute('data-price')) || 0;
        var qtyInput = document.getElementById('selected_qty');

        if (currentSelectedDishId === id) {
            // Ya estaba seleccionado -> incrementar cantidad en +1 por cada clic adicional
            var currentQty = parseInt(qtyInput.value) || 1;
            qtyInput.value = currentQty + 1;
        } else {
            // Nuevo plato seleccionado -> iniciar en 1
            currentSelectedDishId = id;
            qtyInput.value = 1;

            var allCards = document.querySelectorAll('.product-card');
            for (var i = 0; i < allCards.length; i++) {
                allCards[i].classList.remove('selected-dish');
                var cid = allCards[i].getAttribute('data-id');
                var bdg = document.getElementById('dish_select_badge_' + cid);
                if (bdg) bdg.style.display = 'none';
            }

            cardElem.classList.add('selected-dish');
            document.getElementById('selected_product_id').value = id;
            document.getElementById('selected_product_name').value = title + ' (' + sym + ' ' + price.toFixed(2) + ')';
            document.getElementById('btnAddDish').disabled = false;
        }

        syncBadgeWithQty();
    }

    // Botones - y + en el input de Cantidad antes de comandar
    function changeSelectedQty(delta) {
        var qtyInput = document.getElementById('selected_qty');
        var currentQty = parseInt(qtyInput.value) || 1;
        var nextQty = Math.max(1, currentQty + delta);
        qtyInput.value = nextQty;
        syncBadgeWithQty();
    }

    function syncBadgeWithQty() {
        if (!currentSelectedDishId) return;
        var qty = parseInt(document.getElementById('selected_qty').value) || 1;
        var badge = document.getElementById('dish_select_badge_' + currentSelectedDishId);
        if (badge) {
            badge.innerText = qty + 'x seleccionado';
            badge.style.display = 'inline-block';
        }
    }

    // Enviar +1 directo al tocar el botón "+1 Directo" (100% AJAX sin recargar)
    function quickAddFromCard(btnElem) {
        var cardElem = $(btnElem).closest('.product-card')[0];
        var id = cardElem.getAttribute('data-id');
        sendAddDishRequest(id, 1, '');
    }

    // Enviar el formulario de Comandar 100% por AJAX sin recargar la página
    function submitAddDishAjax(e) {
        if (e) e.preventDefault();
        var prodId = document.getElementById('selected_product_id').value;
        var qty = parseInt(document.getElementById('selected_qty').value) || 1;
        var notes = document.getElementById('selected_notes').value || '';
        if (!prodId) return false;

        sendAddDishRequest(prodId, qty, notes);
        return false;
    }

    function sendAddDishRequest(prodId, qty, notes) {
        var btn = document.getElementById('btnAddDish');
        if (btn) btn.disabled = true;

        $.ajax({
            url: "{{ route('restaurant.orders.items.add', $order) }}",
            method: "POST",
            dataType: "json",
            data: {
                _token: csrfToken,
                product_id: prodId,
                quantity: qty,
                notes: notes
            },
            success: function(res) {
                if (res && res.ok) {
                    renderOrUpdateOrderedItemRow(res);
                    updateOrderTotalsUI(res.order_subtotal, res.total_items);

                    // Limpiar selección actual
                    currentSelectedDishId = null;
                    document.getElementById('selected_product_id').value = '';
                    document.getElementById('selected_product_name').value = '';
                    document.getElementById('selected_qty').value = 1;
                    document.getElementById('selected_notes').value = '';

                    document.querySelectorAll('.product-card').forEach(function(c) {
                        c.classList.remove('selected-dish');
                        var cid = c.getAttribute('data-id');
                        var bdg = document.getElementById('dish_select_badge_' + cid);
                        if (bdg) bdg.style.display = 'none';
                    });
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo comandar el plato.';
                alert(msg);
                if (btn && document.getElementById('selected_product_id').value) {
                    btn.disabled = false;
                }
            }
        });
    }

    // Construir o actualizar dinámicamente la fila del plato en la comanda derecha
    function renderOrUpdateOrderedItemRow(res) {
        var emptyMsg = document.getElementById('emptyOrderMsg');
        if (emptyMsg) emptyMsg.remove();

        var existingRow = document.getElementById('order_item_row_' + res.item_id);
        if (existingRow) {
            var qtyInput = document.getElementById('ordered_qty_input_' + res.item_id);
            if (qtyInput) qtyInput.value = res.quantity;
            var subEl = document.getElementById('ordered_line_sub_' + res.item_id);
            if (subEl) subEl.innerText = sym + ' ' + parseFloat(res.line_subtotal).toFixed(2);
            return;
        }

        var notesHtml = '';
        if (res.notes) {
            notesHtml = '<small class="text-danger font-weight-bold d-block"><i class="fas fa-comment-dots"></i> ' + $('<div>').text(res.notes).html() + '</small>';
        }

        var priceFmt = parseFloat(res.price).toFixed(2);
        var lineSubFmt = parseFloat(res.line_subtotal).toFixed(2);
        var safeName = $('<div>').text(res.product_name).html();

        var html = '' +
            '<div class="ticket-item" id="order_item_row_' + res.item_id + '">' +
                '<div style="flex: 1; min-width: 0;" class="pr-1">' +
                    '<div class="font-weight-bold text-dark text-truncate" style="font-size:13px;" title="' + safeName + '">' +
                        safeName +
                    '</div>' +
                    notesHtml +
                    '<div class="d-flex align-items-center mt-1" style="gap: 6px;">' +
                        '<small class="text-muted">' + sym + ' ' + priceFmt + ' c/u</small>' +
                        '<button type="button" id="item_status_btn_' + res.item_id + '" data-status="EN_COCINA" ' +
                                'onclick="toggleOrderItemStatusAjax(' + res.item_id + ')" ' +
                                'class="badge border-0 py-1 px-2 badge-warning text-dark" style="cursor:pointer; font-size:10px;">' +
                            '🔥 Cocina' +
                        '</button>' +
                    '</div>' +
                '</div>' +
                '<div class="d-flex align-items-center">' +
                    '<button type="button" class="qty-btn mr-1" onclick="updateOrderedItemQtyAjax(' + res.item_id + ', -1)">-</button>' +
                    '<input type="number" min="1" id="ordered_qty_input_' + res.item_id + '" value="' + res.quantity + '" ' +
                           'onchange="setOrderedItemQtyAjax(' + res.item_id + ', this.value)" ' +
                           'class="form-control form-control-sm text-center font-weight-bold px-1" style="width: 44px; height: 30px;">' +
                    '<button type="button" class="qty-btn ml-1" onclick="updateOrderedItemQtyAjax(' + res.item_id + ', 1)">+</button>' +
                '</div>' +
                '<div class="d-flex align-items-center justify-content-end" style="gap: 8px; min-width: 105px;">' +
                    '<strong class="text-primary" style="font-size:13px;" id="ordered_line_sub_' + res.item_id + '">' +
                        sym + ' ' + lineSubFmt +
                    '</strong>' +
                    '<button type="button" class="btn btn-xs btn-outline-danger p-1 px-2" onclick="removeOrderedItemAjax(' + res.item_id + ')" title="Quitar de la comanda">' +
                        '<i class="fas fa-trash-alt"></i>' +
                    '</button>' +
                '</div>' +
            '</div>';

        $('#orderItemsContainer').append(html);
    }

    // =========================================================================
    // CONTROLES AJAX EN LA LISTA DE PLATOS YA COMANDADOS (SIN RECARGAR PÁGINA)
    // =========================================================================
    function updateOrderedItemQtyAjax(itemId, delta) {
        var input = document.getElementById('ordered_qty_input_' + itemId);
        if (!input) return;
        var newQty = (parseInt(input.value) || 1) + delta;
        if (newQty <= 0) {
            removeOrderedItemAjax(itemId);
            return;
        }
        input.value = newQty;
        setOrderedItemQtyAjax(itemId, newQty);
    }

    function setOrderedItemQtyAjax(itemId, qty) {
        var newQty = Math.max(1, parseInt(qty) || 1);
        $.ajax({
            url: "{{ url('restaurant/items') }}/" + itemId + "/qty",
            method: "POST",
            dataType: "json",
            data: {
                _token: csrfToken,
                quantity: newQty
            },
            success: function(res) {
                if (res.ok) {
                    var subEl = document.getElementById('ordered_line_sub_' + itemId);
                    if (subEl) subEl.innerText = sym + ' ' + parseFloat(res.line_subtotal).toFixed(2);
                    updateOrderTotalsUI(res.order_subtotal, res.total_items);
                }
            }
        });
    }

    function removeOrderedItemAjax(itemId) {
        var row = document.getElementById('order_item_row_' + itemId);
        if (row) row.remove();

        $.ajax({
            url: "{{ url('restaurant/items') }}/" + itemId,
            method: "POST",
            dataType: "json",
            data: {
                _token: csrfToken,
                _method: "DELETE"
            },
            success: function(res) {
                if (res.ok) {
                    updateOrderTotalsUI(res.order_subtotal, res.total_items);
                }
            }
        });
    }

    function toggleOrderItemStatusAjax(itemId) {
        var btn = document.getElementById('item_status_btn_' + itemId);
        if (!btn) return;
        var current = btn.getAttribute('data-status') || 'EN_COCINA';
        var next = current === 'EN_COCINA' ? 'LISTO' : (current === 'LISTO' ? 'SERVIDO' : 'EN_COCINA');

        btn.setAttribute('data-status', next);
        btn.classList.remove('badge-warning', 'text-dark', 'badge-primary', 'badge-success');
        if (next === 'EN_COCINA') {
            btn.classList.add('badge-warning', 'text-dark');
            btn.innerHTML = '🔥 Cocina';
        } else if (next === 'LISTO') {
            btn.classList.add('badge-primary');
            btn.innerHTML = '🔔 ¡Listo!';
        } else {
            btn.classList.add('badge-success');
            btn.innerHTML = '✅ Servido';
        }

        $.ajax({
            url: "{{ url('restaurant/items') }}/" + itemId + "/status",
            method: "POST",
            dataType: "json",
            data: {
                _token: csrfToken,
                kitchen_status: next
            }
        });
    }

    function updateOrderTotalsUI(newSubtotal, totalItems) {
        baseSubtotal = parseFloat(newSubtotal) || 0;
        document.getElementById('badge_total_items').innerText = totalItems + ' ítems';
        document.getElementById('chk_received').value = baseSubtotal.toFixed(2);
        recalcCheckout();

        var btnCheckout = document.getElementById('btnCheckoutOrder');
        var cancelForm = document.getElementById('cancelTableForm');
        if (totalItems <= 0) {
            if (btnCheckout) btnCheckout.disabled = true;
            if (cancelForm) cancelForm.style.display = 'block';
        } else {
            if (btnCheckout) btnCheckout.disabled = false;
            if (cancelForm) cancelForm.style.display = 'none';
        }
    }

    function filterByCategory(catId, btn) {
        document.querySelectorAll('.category-pill').forEach(function(b) {
            b.classList.remove('active');
        });
        btn.classList.add('active');

        document.querySelectorAll('.product-card').forEach(function(card) {
            if (catId === 'ALL' || card.getAttribute('data-category') === catId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterMenu() {
        var q = document.getElementById('searchDish').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(function(card) {
            var name = card.getAttribute('data-name') || '';
            card.style.display = name.indexOf(q) !== -1 ? 'flex' : 'none';
        });
    }

    function recalcCheckout() {
        var disc = parseFloat(document.getElementById('chk_discount').value) || 0;
        var tip = parseFloat(document.getElementById('chk_tip').value) || 0;
        var rec = parseFloat(document.getElementById('chk_received').value) || 0;

        var finalTotal = Math.max(0, baseSubtotal - disc + tip);
        var change = Math.max(0, rec - finalTotal);

        document.getElementById('lbl_final_total').innerText = sym + ' ' + finalTotal.toFixed(2);
        document.getElementById('header_order_total').innerText = sym + ' ' + finalTotal.toFixed(2);
        document.getElementById('lbl_final_change').innerText = sym + ' ' + change.toFixed(2);
    }
</script>
@endsection
