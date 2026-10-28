@extends('layouts.admin')
@section('title', 'Caja Rápida - POS Táctil')

@section('styles')
<style>
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
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
        gap: 12px;
        max-height: 620px;
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
    .product-card.in-cart {
        border-color: #10b981;
        background: #f0fdf4;
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
    .ticket-items {
        min-height: 210px;
        max-height: 310px;
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
    }
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
    }
    .qty-btn:hover {
        background: #4b49ac;
        color: #fff;
    }
    .payment-pill {
        flex: 1 1 30%;
        padding: 8px 10px;
        border: 2px solid #cbd5e1;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        font-weight: 700;
        font-size: 12px;
        background: #fff;
        transition: all 0.15s;
    }
    .payment-pill.active {
        border-color: #4b49ac;
        background: #eef2ff;
        color: #4b49ac;
    }
    .quick-cash-btn {
        padding: 4px 9px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 6px;
        margin: 1px;
    }
</style>
@endsection

@section('content')
@php
    $rubro = $activeCompany->rubro_config;
    $bType = $activeCompany->business_type ?? 'GENERAL';
    $sym = $activeCompany->currency_symbol ?? 'Bs.';
@endphp
<div class="content-wrapper">
    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <span class="badge badge-primary mb-1"><i class="{{ $rubro['icon'] }} mr-1"></i> POS Especializado: {{ $rubro['label'] }}</span>
            <h3 class="page-title mb-0 font-weight-bold">
                <i class="fas fa-cash-register text-warning mr-2"></i> Caja Rápida — {{ $rubro['short_label'] }}
            </h3>
        </div>
        <div class="mt-2 mt-md-0">
            @if($bType === 'RESTAURANTE')
            <a href="{{ route('restaurant.index') }}" class="btn btn-danger btn-sm font-weight-bold mr-2">
                <i class="fas fa-utensils"></i> Mapa de Mesas
            </a>
            @endif
            <a href="{{ route('sales.create') }}" class="btn btn-outline-primary btn-sm mr-2">
                <i class="fas fa-file-invoice"></i> Venta Tradicional
            </a>
            @if($activeCompany->enable_credits ?? true)
            <a href="{{ route('sales.credit.create') }}" class="btn btn-outline-info btn-sm mr-2">
                <i class="fas fa-hand-holding-usd"></i> Venta a Crédito
            </a>
            @endif
            <a href="{{ route('sales.index') }}" class="btn btn-light btn-sm border">
                <i class="fas fa-list"></i> Historial de Ventas
            </a>
        </div>
    </div>

    <form id="posForm" action="{{ route('sales.store') }}" method="POST">
        @csrf
        <input type="hidden" name="sale_type" value="POS_RAPIDO">
        <input type="hidden" name="payment_condition" value="CONTADO">
        <input type="hidden" name="payment_method_id" id="input_payment_method_id" value="{{ $paymentMethods->first()->id ?? 1 }}">
        <input type="hidden" name="payment_method" id="input_payment_method" value="{{ $paymentMethods->first()->name ?? 'Efectivo (Bs.)' }}">
        <input type="hidden" name="total" id="input_total" value="0.00">
        <input type="hidden" name="cash_change" id="input_cash_change" value="0.00">

        <div class="row">
            <!-- ========================================================= -->
            <!-- COLUMNA IZQUIERDA (7 COLUMNAS): CATÁLOGO DE PRODUCTOS     -->
            <!-- ========================================================= -->
            <div class="col-lg-7 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <!-- BUSCADOR CON CÓDIGO DE BARRAS -->
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-barcode fa-lg text-primary"></i>
                                </span>
                            </div>
                            <input type="text" id="barcodeSearch" class="form-control"
                                   placeholder="Escanee código de barras o escriba para buscar plato/producto..."
                                   onkeyup="filterPosProducts(event)">
                        </div>

                        <!-- CATEGORÍAS TIPO PILLS (ENVUELTAS PARA NO EMPUJAR LA COLUMNA DERECHA) -->
                        <div class="category-pills">
                            <button type="button" class="category-pill active" onclick="filterPosByCategory('ALL', this)">
                                <i class="fas fa-th-large mr-1"></i> Todos ({{ $products->count() }})
                            </button>
                            @foreach($categories as $cat)
                            <button type="button" class="category-pill" onclick="filterPosByCategory('{{ $cat->id }}', this)">
                                {{ $cat->name }}
                            </button>
                            @endforeach
                        </div>

                        <!-- GRID DE PRODUCTOS -->
                        <div class="product-grid" id="productGrid">
                            @forelse($products as $prod)
                            @php
                                $isUnlimited = $prod->isService() || $prod->item_type === 'COMBO' || ($bType === 'RESTAURANTE' && $prod->stock <= 0);
                                $posStock = $isUnlimited ? 999999 : $prod->stock;
                            @endphp
                            <div class="product-card"
                                 id="pos_card_{{ $prod->id }}"
                                 data-id="{{ $prod->id }}"
                                 data-code="{{ strtolower($prod->code ?? $prod->id) }}"
                                 data-name="{{ strtolower($prod->name) }}"
                                 data-title="{{ $prod->name }}"
                                 data-price="{{ $prod->sell_price }}"
                                 data-stock="{{ $posStock }}"
                                 data-category="{{ $prod->category_id }}"
                                 onclick="addToCartFromCard(this)">

                                <span class="product-price-badge">{{ $sym }} {{ number_format($prod->sell_price, 2) }}</span>
                                <span class="badge badge-danger position-absolute" id="card_qty_badge_{{ $prod->id }}"
                                      style="top:8px; left:8px; display:none; font-size:11px; padding:4px 7px; border-radius:10px; z-index:2;">
                                    0
                                </span>

                                <div class="product-img-box">
                                    @if($prod->image && file_exists(public_path('image/' . $prod->image)))
                                        <img src="{{ asset('image/' . $prod->image) }}" alt="{{ $prod->name }}">
                                    @else
                                        <i class="fas {{ $isUnlimited ? 'fa-utensils text-primary' : 'fa-box-open text-secondary' }} fa-2x"></i>
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
                                        <span class="badge badge-warning text-dark mt-1" style="font-size:10px;"><i class="fas fa-layer-group"></i> Combo Promocional</span>
                                    @elseif($isUnlimited)
                                        <span class="badge badge-info mt-1" style="font-size:10px;">{{ $bType === 'RESTAURANTE' ? 'Plato / Cocina' : 'Servicio' }}</span>
                                    @else
                                        <span class="badge badge-success mt-1" style="font-size:10px;">Stock: {{ $prod->stock }} {{ $prod->unit_measure ?? 'UND' }}</span>
                                    @endif

                                    <div class="mt-2">
                                        <span class="btn btn-xs btn-primary btn-block font-weight-bold py-1">
                                            <i class="fas fa-plus mr-1"></i> Agregar
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="fas fa-boxes fa-3x mb-3"></i>
                                <h5>No hay productos activos disponibles.</h5>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- COLUMNA DERECHA (5 COLUMNAS): CARRITO Y COBRO DE VENTA    -->
            <!-- ========================================================= -->
            <div class="col-lg-5 mb-3">
                <div class="card border-0 shadow-sm" style="border-top: 4px solid #4b49ac !important;">
                    <div class="card-body p-3">
                        <!-- SELECCIÓN DE CLIENTE -->
                        <div class="form-group mb-2">
                            <label class="font-weight-bold text-dark small mb-1">
                                <i class="fas fa-user mr-1 text-primary"></i> Cliente:
                            </label>
                            <select name="client_id" id="client_id" class="form-control form-control-sm" required>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} (CI/NIT: {{ $client->ci ?: $client->nit }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- CAMPO OPERATIVO SEGÚN RUBRO -->
                        <div class="form-group mb-2 p-2 rounded border" style="background:#f8fafc;">
                            @if($bType === 'RESTAURANTE')
                                <label class="font-weight-bold text-primary small mb-1 d-block">
                                    <i class="fas fa-utensils mr-1"></i> Modalidad de Pedido Rápido:
                                </label>
                                <div class="btn-group btn-group-sm btn-block mb-1" role="group">
                                    <button type="button" class="btn btn-outline-primary font-weight-bold" onclick="document.getElementById('pos_notes').value = 'EN MESA: '">🍽️ En Mesa</button>
                                    <button type="button" class="btn btn-outline-warning text-dark font-weight-bold" onclick="document.getElementById('pos_notes').value = 'PARA LLEVAR: '">🛍️ Para Llevar</button>
                                    <button type="button" class="btn btn-outline-info font-weight-bold" onclick="document.getElementById('pos_notes').value = 'DELIVERY: '">🛵 Delivery</button>
                                </div>
                            @else
                                <label class="font-weight-bold text-primary small mb-1">
                                    <i class="{{ $rubro['icon'] }} mr-1"></i> {{ $rubro['pos_notes_label'] }}:
                                </label>
                            @endif
                            <input type="text" name="notes" id="pos_notes" class="form-control form-control-sm"
                                   placeholder="{{ $rubro['pos_notes_placeholder'] }}">
                        </div>

                        <!-- CABECERA DEL CARRITO -->
                        <div class="d-flex justify-content-between align-items-center mt-2 mb-1">
                            <h6 class="font-weight-bold mb-0 text-dark">
                                <i class="fas fa-shopping-cart mr-1 text-primary"></i> Carrito de Venta
                                <span class="badge badge-primary ml-1" id="cartCountBadge">0 ítems</span>
                            </h6>
                            <button type="button" class="btn btn-outline-danger btn-xs font-weight-bold" onclick="clearCart()">
                                <i class="fas fa-trash-alt"></i> Vaciar
                            </button>
                        </div>

                        <!-- LISTA DE PRODUCTOS EN EL CARRITO (CON BOTONES - / +) -->
                        <div class="ticket-items" id="ticketItemsContainer">
                            <div id="emptyTicket" class="text-center text-muted py-4">
                                <i class="fas fa-shopping-basket fa-2x mb-2 text-secondary d-block"></i>
                                <strong class="small d-block">El carrito está vacío</strong>
                                <small>Haga clic en un producto de la izquierda para agregarlo aquí.</small>
                            </div>
                        </div>

                        <!-- SUBTOTAL, DESCUENTO Y TOTAL -->
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted font-weight-bold">Subtotal:</span>
                            <span class="font-weight-bold" id="lblSubtotal">{{ $sym }} 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-muted font-weight-bold">Descuento Global (%):</span>
                            <input type="number" id="globalDiscount" class="form-control form-control-sm text-right font-weight-bold"
                                   style="width: 75px;" value="0" min="0" max="50" oninput="renderTicket()">
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded mb-2" style="background:#eef2ff; border:1px solid #c7d2fe;">
                            <span class="font-weight-bold text-dark">TOTAL A PAGAR:</span>
                            <span class="h4 mb-0 font-weight-bold text-primary" id="lblTotal">{{ $sym }} 0.00</span>
                        </div>

                        <!-- MÉTODOS DE PAGO -->
                        <label class="small font-weight-bold mb-1 d-block">Forma de Pago:</label>
                        <div class="d-flex flex-wrap mb-2" style="gap: 6px;">
                            @foreach($paymentMethods->whereIn('payment_condition', ['CONTADO', 'AMBOS']) as $idx => $pm)
                            <div class="payment-pill {{ $idx === 0 ? 'active' : '' }}"
                                 data-name="{{ $pm->name }}"
                                 data-id="{{ $pm->id }}"
                                 data-code="{{ $pm->code }}"
                                 data-ref="{{ $pm->requires_reference ? '1' : '0' }}"
                                 onclick="selectPaymentPill(this)">
                                <i class="{{ $pm->icon ?? 'fas fa-money-bill' }} mr-1"></i> {{ $pm->name }}
                            </div>
                            @endforeach
                        </div>

                        <!-- CAMPO DE REFERENCIA -->
                        <div id="referenceFieldBox" class="form-group mb-2" style="display: none;">
                            <label class="small font-weight-bold mb-0 text-dark">N° Comprobante / Referencia QR / Tarjeta:</label>
                            <input type="text" name="payment_reference" id="pos_payment_reference" class="form-control form-control-sm" placeholder="Ej: 9482716">
                        </div>

                        <!-- CALCULADORA DE EFECTIVO Y VUELTO -->
                        <div id="cashCalculatorBox" class="p-2 border rounded bg-light mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="small font-weight-bold mb-0">Efectivo Recibido ({{ $sym }}):</label>
                                <input type="number" step="0.5" id="cashReceived" name="cash_received"
                                       class="form-control form-control-sm text-right font-weight-bold" style="width: 115px;"
                                       placeholder="Pago exacto" oninput="calcChange()">
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <button type="button" class="btn btn-secondary btn-xs quick-cash-btn" onclick="quickCash('EXACT')">Exacto</button>
                                    <button type="button" class="btn btn-outline-secondary btn-xs quick-cash-btn" onclick="quickCash(20)">20</button>
                                    <button type="button" class="btn btn-outline-secondary btn-xs quick-cash-btn" onclick="quickCash(50)">50</button>
                                    <button type="button" class="btn btn-outline-secondary btn-xs quick-cash-btn" onclick="quickCash(100)">100</button>
                                    <button type="button" class="btn btn-outline-secondary btn-xs quick-cash-btn" onclick="quickCash(200)">200</button>
                                </div>
                                <div class="text-right">
                                    <span class="small text-muted d-block">Cambio / Vuelto:</span>
                                    <strong class="text-success h6 mb-0" id="lblChange">{{ $sym }} 0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- BOTÓN COBRAR Y GUARDAR VENTA -->
                        <button type="button" id="btnFinalize" class="btn btn-success btn-block btn-lg font-weight-bold shadow"
                                disabled onclick="submitSale()">
                            <i class="fas fa-check-circle mr-1"></i> COBRAR Y FINALIZAR VENTA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    var cart = [];
    var sym = "{{ $sym }}";

    // Agregar al carrito al hacer clic en cualquier tarjeta de producto
    function addToCartFromCard(cardElem) {
        var id = parseInt(cardElem.getAttribute('data-id'));
        var title = cardElem.getAttribute('data-title');
        var price = parseFloat(cardElem.getAttribute('data-price')) || 0;
        var stock = parseInt(cardElem.getAttribute('data-stock')) || 999999;

        addToCart(id, title, price, stock);
    }

    function addToCart(id, name, price, stock) {
        id = parseInt(id);
        stock = isNaN(parseInt(stock)) ? 999999 : parseInt(stock);
        price = parseFloat(price) || 0;

        var existing = null;
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === id) {
                existing = cart[i];
                break;
            }
        }

        if (existing) {
            if (existing.qty + 1 > stock) {
                alert('Stock límite alcanzado (' + stock + ' disponibles).');
                return;
            }
            existing.qty += 1;
        } else {
            if (stock < 1) {
                alert('Este producto no tiene stock disponible.');
                return;
            }
            cart.push({
                id: id,
                name: name,
                price: price,
                stock: stock,
                qty: 1
            });
        }

        renderTicket();
    }

    function updateQty(id, delta) {
        id = parseInt(id);
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === id) {
                var newQty = cart[i].qty + delta;
                if (newQty > cart[i].stock) {
                    alert('Stock máximo disponible: ' + cart[i].stock);
                    return;
                }
                if (newQty <= 0) {
                    cart.splice(i, 1);
                } else {
                    cart[i].qty = newQty;
                }
                break;
            }
        }
        renderTicket();
    }

    function setDirectQty(id, val) {
        id = parseInt(id);
        var newQty = parseInt(val);
        if (isNaN(newQty) || newQty < 1) newQty = 1;

        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === id) {
                if (newQty > cart[i].stock) {
                    alert('Stock máximo disponible: ' + cart[i].stock);
                    newQty = cart[i].stock;
                }
                cart[i].qty = newQty;
                break;
            }
        }
        renderTicket();
    }

    function removeFromCart(id) {
        id = parseInt(id);
        var updated = [];
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id !== id) {
                updated.push(cart[i]);
            }
        }
        cart = updated;
        renderTicket();
    }

    function clearCart() {
        cart = [];
        renderTicket();
    }

    function renderTicket() {
        var container = document.getElementById('ticketItemsContainer');
        var btnFinalize = document.getElementById('btnFinalize');
        var discInput = document.getElementById('globalDiscount');
        var discPercent = parseFloat(discInput ? discInput.value : 0) || 0;
        if (discPercent < 0) discPercent = 0;
        if (discPercent > 50) discPercent = 50;

        // Actualizar insignias en las tarjetas del catálogo
        var allCards = document.querySelectorAll('.product-card');
        for (var c = 0; c < allCards.length; c++) {
            allCards[c].classList.remove('in-cart');
            var cid = allCards[c].getAttribute('data-id');
            var bdg = document.getElementById('card_qty_badge_' + cid);
            if (bdg) {
                bdg.style.display = 'none';
                bdg.innerText = '0';
            }
        }

        var totalUnits = 0;
        for (var k = 0; k < cart.length; k++) {
            totalUnits += cart[k].qty;
            var cardEl = document.getElementById('pos_card_' + cart[k].id);
            if (cardEl) cardEl.classList.add('in-cart');
            var badgeEl = document.getElementById('card_qty_badge_' + cart[k].id);
            if (badgeEl) {
                badgeEl.innerText = cart[k].qty + ' en carrito';
                badgeEl.style.display = 'inline-block';
            }
        }

        document.getElementById('cartCountBadge').innerText = totalUnits + (totalUnits === 1 ? ' ítem' : ' ítems');

        if (cart.length === 0) {
            container.innerHTML =
                '<div id="emptyTicket" class="text-center text-muted py-4">' +
                    '<i class="fas fa-shopping-basket fa-2x mb-2 text-secondary d-block"></i>' +
                    '<strong class="small d-block">El carrito está vacío</strong>' +
                    '<small>Haga clic en un producto de la izquierda para agregarlo aquí.</small>' +
                '</div>';
            btnFinalize.disabled = true;
            recalculateTotals();
            return;
        }

        btnFinalize.disabled = false;

        var html = '';
        for (var i = 0; i < cart.length; i++) {
            var item = cart[i];
            var sub = item.qty * item.price;
            html +=
                '<div class="ticket-item">' +
                    '<div style="flex:1; min-width:0;" class="pr-2">' +
                        '<div class="font-weight-bold text-dark text-truncate" style="font-size:13px;">' + item.name + '</div>' +
                        '<div class="small text-muted">' + sym + ' ' + item.price.toFixed(2) + ' c/u</div>' +
                    '</div>' +
                    '<div class="d-flex align-items-center pr-2">' +
                        '<button type="button" class="qty-btn mr-1" onclick="updateQty(' + item.id + ', -1)">-</button>' +
                        '<input type="number" min="1" max="' + item.stock + '" value="' + item.qty + '" ' +
                               'onchange="setDirectQty(' + item.id + ', this.value)" ' +
                               'class="form-control form-control-sm text-center font-weight-bold px-1" style="width:48px; height:30px;">' +
                        '<button type="button" class="qty-btn ml-1" onclick="updateQty(' + item.id + ', 1)">+</button>' +
                    '</div>' +
                    '<div class="d-flex align-items-center justify-content-end" style="gap:8px; min-width:105px;">' +
                        '<strong class="text-primary" style="font-size:13px;">' + sym + ' ' + sub.toFixed(2) + '</strong>' +
                        '<button type="button" class="btn btn-xs btn-outline-danger p-1 px-2" onclick="removeFromCart(' + item.id + ')" title="Quitar del carrito">' +
                            '<i class="fas fa-trash-alt"></i>' +
                        '</button>' +
                    '</div>' +
                    '<input type="hidden" name="product_id[]" value="' + item.id + '">' +
                    '<input type="hidden" name="quantity[]" value="' + item.qty + '">' +
                    '<input type="hidden" name="price[]" value="' + item.price.toFixed(2) + '">' +
                    '<input type="hidden" name="discount[]" value="' + discPercent + '">' +
                '</div>';
        }

        container.innerHTML = html;
        recalculateTotals();
    }

    function recalculateTotals() {
        var subtotal = 0;
        for (var i = 0; i < cart.length; i++) {
            subtotal += cart[i].qty * cart[i].price;
        }
        var discPercent = parseFloat(document.getElementById('globalDiscount').value) || 0;
        if (discPercent < 0) discPercent = 0;
        if (discPercent > 50) discPercent = 50;

        var discount = (subtotal * discPercent) / 100;
        var total = Math.max(0, subtotal - discount);

        document.getElementById('lblSubtotal').innerText = sym + ' ' + subtotal.toFixed(2);
        document.getElementById('lblTotal').innerText = sym + ' ' + total.toFixed(2);
        document.getElementById('input_total').value = total.toFixed(2);

        calcChange();
    }

    function selectPaymentPill(elem) {
        var pills = document.querySelectorAll('.payment-pill');
        for (var i = 0; i < pills.length; i++) {
            pills[i].classList.remove('active');
        }
        elem.classList.add('active');

        var methodName = elem.getAttribute('data-name') || 'Efectivo';
        var methodId = elem.getAttribute('data-id') || '1';
        var methodCode = elem.getAttribute('data-code') || '';
        var requiresRef = elem.getAttribute('data-ref') === '1';

        document.getElementById('input_payment_method').value = methodName;
        document.getElementById('input_payment_method_id').value = methodId;

        document.getElementById('referenceFieldBox').style.display = requiresRef ? 'block' : 'none';
        if (!requiresRef) {
            document.getElementById('pos_payment_reference').value = '';
        }

        var isCash = (methodCode === 'EFECTIVO' || methodName.toLowerCase().indexOf('efectivo') !== -1);
        document.getElementById('cashCalculatorBox').style.display = isCash ? 'block' : 'none';
        if (!isCash) {
            var total = parseFloat(document.getElementById('input_total').value) || 0;
            document.getElementById('cashReceived').value = total.toFixed(2);
            document.getElementById('lblChange').innerText = sym + ' 0.00';
            document.getElementById('input_cash_change').value = '0.00';
        }
    }

    function calcChange() {
        var total = parseFloat(document.getElementById('input_total').value) || 0;
        var received = parseFloat(document.getElementById('cashReceived').value) || 0;
        var change = Math.max(0, received - total);

        document.getElementById('lblChange').innerText = sym + ' ' + change.toFixed(2);
        document.getElementById('input_cash_change').value = change.toFixed(2);
    }

    function quickCash(val) {
        var total = parseFloat(document.getElementById('input_total').value) || 0;
        if (val === 'EXACT') {
            document.getElementById('cashReceived').value = total.toFixed(2);
        } else {
            document.getElementById('cashReceived').value = parseFloat(val).toFixed(2);
        }
        calcChange();
    }

    function filterPosByCategory(catId, btnElem) {
        var pills = document.querySelectorAll('.category-pill');
        for (var i = 0; i < pills.length; i++) {
            pills[i].classList.remove('active');
        }
        btnElem.classList.add('active');

        var cards = document.querySelectorAll('.product-card');
        for (var j = 0; j < cards.length; j++) {
            var cCat = cards[j].getAttribute('data-category');
            if (catId === 'ALL' || cCat === catId) {
                cards[j].style.display = 'flex';
            } else {
                cards[j].style.display = 'none';
            }
        }
    }

    function filterPosProducts(e) {
        var input = document.getElementById('barcodeSearch');
        var term = (input.value || '').toLowerCase().trim();
        var cards = document.querySelectorAll('.product-card');

        if (e && e.keyCode === 13 && term !== '') {
            e.preventDefault();
            for (var i = 0; i < cards.length; i++) {
                var code = cards[i].getAttribute('data-code') || '';
                var name = cards[i].getAttribute('data-name') || '';
                if (code === term || name === term) {
                    addToCartFromCard(cards[i]);
                    input.value = '';
                    filterPosProducts(null);
                    return;
                }
            }
        }

        for (var j = 0; j < cards.length; j++) {
            var cName = cards[j].getAttribute('data-name') || '';
            var cCode = cards[j].getAttribute('data-code') || '';
            if (cName.indexOf(term) !== -1 || cCode.indexOf(term) !== -1) {
                cards[j].style.display = 'flex';
            } else {
                cards[j].style.display = 'none';
            }
        }
    }

    function submitSale() {
        if (cart.length === 0) {
            alert('Debe agregar al menos un producto al carrito.');
            return;
        }

        var paymentMethod = document.getElementById('input_payment_method').value || 'Efectivo';
        var total = parseFloat(document.getElementById('input_total').value) || 0;
        var cashInput = document.getElementById('cashReceived');
        var rawReceived = cashInput.value;

        if (rawReceived === '' || parseFloat(rawReceived) === 0) {
            cashInput.value = total.toFixed(2);
            calcChange();
        }

        var received = parseFloat(cashInput.value) || 0;
        if (paymentMethod.toUpperCase().indexOf('EFECTIVO') !== -1 && received < total) {
            alert('El monto en efectivo recibido es menor al total a pagar.');
            return;
        }

        var btn = document.getElementById('btnFinalize');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> PROCESANDO VENTA...';
        document.getElementById('posForm').submit();
    }
</script>
@endsection
