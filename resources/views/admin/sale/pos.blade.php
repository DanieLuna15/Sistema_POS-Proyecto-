@extends('layouts.admin')
@section('title', 'Caja Rápida - POS Táctil')

@section('styles')
<style>
    .pos-container {
        display: flex;
        gap: 20px;
        height: calc(100vh - 160px);
        min-height: 650px;
    }
    .pos-catalog {
        flex: 1 1 65%;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 20px;
        overflow: hidden;
    }
    .pos-ticket {
        flex: 0 0 35%;
        max-width: 450px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .category-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 10px;
        margin-bottom: 15px;
        scrollbar-width: thin;
    }
    .category-pill {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        background: #f8f9fa;
        color: #555;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .category-pill.active, .category-pill:hover {
        background: #4b49ac;
        color: #fff;
        border-color: #4b49ac;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: 15px;
        overflow-y: auto;
        padding-right: 5px;
        flex: 1;
    }
    .product-card {
        border: 1px solid #edf0f5;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        background: #fff;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(75, 73, 172, 0.12);
        border-color: #4b49ac;
    }
    .product-img-box {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }
    .product-img-box img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        border-radius: 6px;
    }
    .product-price-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #392c70;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
    }
    .ticket-items {
        flex: 1;
        overflow-y: auto;
        margin-top: 15px;
        margin-bottom: 15px;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
        padding: 10px 0;
    }
    .ticket-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .qty-btn {
        width: 26px;
        height: 26px;
        padding: 0;
        border-radius: 50%;
        border: 1px solid #ced4da;
        background: #fff;
        font-weight: bold;
        line-height: 24px;
        text-align: center;
    }
    .payment-pill {
        flex: 1;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
    }
    .payment-pill.active {
        border-color: #4b49ac;
        background: #edeefd;
        color: #4b49ac;
    }
    .quick-cash-btn {
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 6px;
        margin: 2px;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-cash-register text-warning mr-2"></i> Caja Rápida - POS Táctil
            </h3>
            <span class="text-muted">Atención veloz en mostrador, escaneo y múltiples métodos de pago.</span>
        </div>
        <div>
            <a href="{{ route('sales.create') }}" class="btn btn-outline-primary btn-sm mr-2">
                <i class="fas fa-file-invoice"></i> Venta Tradicional
            </a>
            <a href="{{ route('sales.credit.create') }}" class="btn btn-outline-info btn-sm mr-2">
                <i class="fas fa-hand-holding-usd"></i> Venta a Crédito
            </a>
            <a href="{{ route('sales.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver a Lista
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

        <div class="pos-container">
            <!-- PANEL IZQUIERDO: CATÁLOGO DE PRODUCTOS -->
            <div class="pos-catalog">
                <!-- BUSCADOR CON CÓDIGO DE BARRAS -->
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-right-0">
                            <i class="fas fa-barcode fa-lg text-primary"></i>
                        </span>
                    </div>
                    <input type="text" id="barcodeSearch" class="form-control form-control-lg border-left-0" 
                           placeholder="Escanee código de barras o escriba para buscar producto... (Enter para agregar)">
                </div>

                <!-- CATEGORÍAS TIPO PILLS -->
                <div class="category-pills">
                    <div class="category-pill active" data-category="ALL">
                        <i class="fas fa-th-large mr-1"></i> Todos
                    </div>
                    @foreach($categories as $cat)
                    <div class="category-pill" data-category="{{ $cat->id }}">
                        {{ $cat->name }}
                    </div>
                    @endforeach
                </div>

                <!-- GRID DE PRODUCTOS -->
                <div class="product-grid" id="productGrid">
                    @forelse($products as $prod)
                    <div class="product-card" 
                         data-id="{{ $prod->id }}"
                         data-code="{{ $prod->code ?? $prod->id }}"
                         data-name="{{ $prod->name }}"
                         data-price="{{ $prod->sell_price }}"
                         data-stock="{{ $prod->stock }}"
                         data-category="{{ $prod->category_id }}"
                         onclick="addToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->sell_price }}, {{ $prod->stock }})">
                        
                        <span class="product-price-badge">Bs. {{ number_format($prod->sell_price, 2) }}</span>

                        <div class="product-img-box">
                            @if($prod->image && file_exists(public_path('image/' . $prod->image)))
                                <img src="{{ asset('image/' . $prod->image) }}" alt="{{ $prod->name }}">
                            @else
                                <i class="fas fa-box-open fa-3x text-muted"></i>
                            @endif
                        </div>

                        <div>
                            <div class="font-weight-bold text-dark text-truncate" title="{{ $prod->name }}">
                                {{ $prod->name }}
                            </div>
                            <small class="text-muted d-block">{{ $prod->brand->name ?? 'Genérico' }}</small>
                            <span class="badge badge-success mt-1">Stock: {{ $prod->stock }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fas fa-boxes fa-3x mb-3"></i>
                        <h5>No hay productos activos con stock disponible.</h5>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- PANEL DERECHO: TICKET DE VENTA -->
            <div class="pos-ticket">
                <div>
                    <!-- SELECCIÓN DE CLIENTE -->
                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-dark small mb-1">Cliente:</label>
                        <div class="input-group">
                            <select name="client_id" id="client_id" class="form-control form-control-sm select2" required>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} (CI: {{ $client->ci }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="font-weight-bold mb-0 text-dark">
                            <i class="fas fa-receipt mr-1 text-primary"></i> Detalle del Ticket
                        </h6>
                        <button type="button" class="btn btn-outline-danger btn-xs" onclick="clearCart()">
                            <i class="fas fa-trash-alt"></i> Vaciar
                        </button>
                    </div>
                </div>

                <!-- ITEMS EN EL TICKET -->
                <div class="ticket-items" id="ticketItemsContainer">
                    <div id="emptyTicket" class="text-center text-muted py-5">
                        <i class="fas fa-shopping-basket fa-2x mb-2 text-light"></i>
                        <p class="small mb-0">Haga clic en un producto para agregarlo al ticket.</p>
                    </div>
                    <!-- Items dinámicos se inyectan con JavaScript -->
                </div>

                <!-- TOTALES Y MÉTODOS DE PAGO -->
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal:</span>
                        <span class="font-weight-bold" id="lblSubtotal">Bs. 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Descuento Global (%):</span>
                        <input type="number" id="globalDiscount" class="form-control form-control-sm text-right" 
                               style="width: 80px;" value="0" min="0" max="50" onchange="recalculateTotals()">
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light mb-3">
                        <span class="h5 mb-0 font-weight-bold text-dark">TOTAL A PAGAR:</span>
                        <span class="h4 mb-0 font-weight-bold text-primary" id="lblTotal">Bs. 0.00</span>
                    </div>

                    <!-- MÉTODOS DE PAGO DINÁMICOS -->
                    <label class="small font-weight-bold mb-1">Forma de Pago (Contado):</label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @foreach($paymentMethods->whereIn('payment_condition', ['CONTADO', 'AMBOS']) as $idx => $pm)
                        <div class="payment-pill {{ $idx === 0 ? 'active' : '' }} mr-1 mb-1" 
                             onclick="setPaymentMethod('{{ addslashes($pm->name) }}', {{ $pm->id }}, '{{ $pm->code }}', {{ $pm->requires_reference ? 'true' : 'false' }}, this)">
                            <i class="{{ $pm->icon ?? 'fas fa-money-bill' }} d-block mb-1"></i> {{ $pm->name }}
                        </div>
                        @endforeach
                    </div>

                    <!-- CAMPO DE REFERENCIA SI SE REQUIERE -->
                    <div id="referenceFieldBox" class="form-group mb-2" style="display: none;">
                        <label class="small font-weight-bold mb-0 text-dark">N° Comprobante / Referencia / Voucher:</label>
                        <input type="text" name="payment_reference" id="pos_payment_reference" class="form-control form-control-sm" placeholder="Ej: 9482716">
                    </div>

                    <!-- CALCULADORA DE CAMBIO (SOLO PARA EFECTIVO) -->
                    <div id="cashCalculatorBox" class="p-2 border rounded bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small font-weight-bold mb-0">Monto Recibido:</label>
                            <input type="number" step="0.5" id="cashReceived" name="cash_received" 
                                   class="form-control form-control-sm text-right" style="width: 110px;" 
                                   placeholder="0.00" onkeyup="calcChange()" onchange="calcChange()">
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-secondary btn-xs quick-cash-btn" onclick="quickCash('EXACT')">Exacto</button>
                                <button type="button" class="btn btn-outline-secondary btn-xs quick-cash-btn" onclick="quickCash(20)">20</button>
                                <button type="button" class="btn btn-outline-secondary btn-xs quick-cash-btn" onclick="quickCash(50)">50</button>
                                <button type="button" class="btn btn-outline-secondary btn-xs quick-cash-btn" onclick="quickCash(100)">100</button>
                                <button type="button" class="btn btn-outline-secondary btn-xs quick-cash-btn" onclick="quickCash(200)">200</button>
                            </div>
                            <div class="text-right">
                                <span class="small text-muted d-block">Cambio / Vuelto:</span>
                                <span class="font-weight-bold text-success h6 mb-0" id="lblChange">Bs. 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- BOTÓN FINALIZAR -->
                    <button type="button" id="btnFinalize" class="btn btn-primary btn-block btn-lg font-weight-bold" 
                            disabled onclick="submitSale()">
                        <i class="fas fa-check-circle mr-2"></i> COBRAR Y FINALIZAR VENTA
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let cart = [];
    let productsDb = @json($products);

    // Búsqueda y escaneo por código de barras
    $('#barcodeSearch').on('keyup', function(e) {
        let term = $(this).val().toLowerCase().trim();
        
        // Si presionan Enter (o lector de código de barras emite Enter)
        if (e.keyCode === 13) {
            e.preventDefault();
            let found = productsDb.find(p => (p.code && p.code.toString().toLowerCase() === term) || p.name.toLowerCase() === term);
            if (found) {
                addToCart(found.id, found.name, found.sell_price, found.stock);
                $(this).val('');
                return;
            }
        }

        // Filtrado visual
        $('.product-card').each(function() {
            let name = $(this).data('name').toString().toLowerCase();
            let code = $(this).data('code').toString().toLowerCase();
            if (name.includes(term) || code.includes(term)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Filtro por pestañas de categoría
    $('.category-pill').on('click', function() {
        $('.category-pill').removeClass('active');
        $(this).addClass('active');

        let catId = $(this).data('category');
        if (catId === 'ALL') {
            $('.product-card').show();
        } else {
            $('.product-card').each(function() {
                if ($(this).data('category') == catId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    function addToCart(id, name, price, stock) {
        let existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty + 1 > stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock Límite',
                    text: 'No hay más unidades disponibles de este producto.',
                    timer: 1500
                });
                return;
            }
            existing.qty++;
        } else {
            if (stock < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin Stock',
                    text: 'El producto no tiene stock disponible.'
                });
                return;
            }
            cart.push({
                id: id,
                name: name,
                price: parseFloat(price),
                stock: parseInt(stock),
                qty: 1
            });
        }
        renderTicket();
    }

    function updateQty(id, delta) {
        let item = cart.find(i => i.id === id);
        if (!item) return;

        let newQty = item.qty + delta;
        if (newQty > item.stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Límite',
                text: 'La cantidad supera el stock disponible (' + item.stock + ').',
                timer: 1500
            });
            return;
        }

        if (newQty <= 0) {
            removeFromCart(id);
        } else {
            item.qty = newQty;
            renderTicket();
        }
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderTicket();
    }

    function clearCart() {
        cart = [];
        renderTicket();
    }

    function renderTicket() {
        let container = $('#ticketItemsContainer');
        container.empty();

        if (cart.length === 0) {
            container.append(`
                <div id="emptyTicket" class="text-center text-muted py-5">
                    <i class="fas fa-shopping-basket fa-2x mb-2 text-light"></i>
                    <p class="small mb-0">Haga clic en un producto para agregarlo al ticket.</p>
                </div>
            `);
            $('#btnFinalize').prop('disabled', true);
            recalculateTotals();
            return;
        }

        $('#btnFinalize').prop('disabled', false);

        cart.forEach(item => {
            let sub = item.qty * item.price;
            let html = `
                <div class="ticket-item">
                    <div style="flex: 1; min-width: 0;" class="pr-2">
                        <div class="font-weight-bold text-truncate small">${item.name}</div>
                        <div class="small text-muted">Bs. ${item.price.toFixed(2)} c/u</div>
                    </div>
                    <div class="d-flex align-items-center pr-2">
                        <button type="button" class="qty-btn mr-1" onclick="updateQty(${item.id}, -1)">-</button>
                        <span class="font-weight-bold px-2 small">${item.qty}</span>
                        <button type="button" class="qty-btn ml-1" onclick="updateQty(${item.id}, 1)">+</button>
                    </div>
                    <div class="text-right" style="width: 75px;">
                        <span class="font-weight-bold small text-dark d-block">Bs. ${sub.toFixed(2)}</span>
                        <a href="javascript:void(0)" class="text-danger small" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>

                    <!-- Campos ocultos para enviar al servidor -->
                    <input type="hidden" name="product_id[]" value="${item.id}">
                    <input type="hidden" name="quantity[]" value="${item.qty}">
                    <input type="hidden" name="price[]" value="${item.price.toFixed(2)}">
                    <input type="hidden" name="discount[]" value="0">
                </div>
            `;
            container.append(html);
        });

        recalculateTotals();
    }

    function recalculateTotals() {
        let subtotal = cart.reduce((acc, item) => acc + (item.qty * item.price), 0);
        let discPercent = parseFloat($('#globalDiscount').val()) || 0;
        if (discPercent < 0) discPercent = 0;
        if (discPercent > 50) discPercent = 50;
        
        let discount = (subtotal * discPercent) / 100;
        let total = Math.max(0, subtotal - discount);

        $('#lblSubtotal').text('Bs. ' + subtotal.toFixed(2));
        $('#lblTotal').text('Bs. ' + total.toFixed(2));
        $('#input_total').val(total.toFixed(2));

        calcChange();
    }

    function setPaymentMethod(methodName, methodId, methodCode, requiresRef, elem) {
        $('.payment-pill').removeClass('active');
        $(elem).addClass('active');
        $('#input_payment_method').val(methodName);
        $('#input_payment_method_id').val(methodId);

        if (requiresRef) {
            $('#referenceFieldBox').slideDown();
        } else {
            $('#referenceFieldBox').slideUp();
            $('#pos_payment_reference').val('');
        }

        if (methodCode === 'EFECTIVO' || methodName.toLowerCase().includes('efectivo')) {
            $('#cashCalculatorBox').slideDown();
        } else {
            $('#cashCalculatorBox').slideUp();
            let total = parseFloat($('#input_total').val()) || 0;
            $('#cashReceived').val(total.toFixed(2));
            $('#lblChange').text('Bs. 0.00');
            $('#input_cash_change').val('0.00');
        }
    }

    function calcChange() {
        let total = parseFloat($('#input_total').val()) || 0;
        let received = parseFloat($('#cashReceived').val()) || 0;
        let change = Math.max(0, received - total);

        $('#lblChange').text('Bs. ' + change.toFixed(2));
        $('#input_cash_change').val(change.toFixed(2));
    }

    function quickCash(val) {
        let total = parseFloat($('#input_total').val()) || 0;
        if (val === 'EXACT') {
            $('#cashReceived').val(total.toFixed(2));
        } else {
            $('#cashReceived').val(parseFloat(val).toFixed(2));
        }
        calcChange();
    }

    function submitSale() {
        if (cart.length === 0) {
            Swal.fire('Atención', 'Debe agregar al menos un producto.', 'warning');
            return;
        }

        let paymentMethod = $('#input_payment_method').val();
        let total = parseFloat($('#input_total').val()) || 0;
        let received = parseFloat($('#cashReceived').val()) || 0;

        if (paymentMethod === 'EFECTIVO' && received < total) {
            Swal.fire({
                icon: 'warning',
                title: 'Monto insuficiente',
                text: 'El monto en efectivo recibido es menor al total a pagar.'
            });
            return;
        }

        Swal.fire({
            title: '¿Confirmar cobro de venta?',
            text: 'Total a cobrar: Bs. ' + total.toFixed(2) + ' (' + paymentMethod + ')',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4b49ac',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, finalizar venta',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                $('#posForm').submit();
            }
        });
    }
</script>
@endsection
