@extends('layouts.admin')
@section('title', 'Nueva Venta a Crédito')

@section('content')
<div class="content-wrapper">
    <div class="page-header mb-3">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-hand-holding-usd text-info mr-2"></i> Registro de Venta a Crédito
            </h3>
            <span class="text-muted">Financiamiento en cuotas, cálculo de cuota inicial y cronograma de cobros.</span>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sales.credits') }}">Cuentas por Cobrar</a></li>
                <li class="breadcrumb-item active">Nueva Venta a Crédito</li>
            </ol>
        </nav>
    </div>

    <form id="creditSaleForm" action="{{ route('sales.store') }}" method="POST">
        @csrf
        <input type="hidden" name="sale_type" value="CREDITO">
        <input type="hidden" name="payment_condition" value="CREDITO">
        <input type="hidden" name="total" id="total_pagar" value="0.00">

        <div class="row">
            <!-- 1. PERFIL Y EVALUACIÓN DEL CLIENTE -->
            <div class="col-12 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 5px solid #17a2b8 !important;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <label class="font-weight-bold text-dark mb-1">
                                    <i class="fas fa-user-check text-info mr-1"></i> Seleccionar Cliente:
                                </label>
                                <select name="client_id" id="client_id" class="form-control" required onchange="onClientSelected()">
                                    <option value="" disabled selected>-- Elija un cliente --</option>
                                    @foreach($clients as $c)
                                    @php
                                        $debt = $c->sales ? $c->sales->sum('balance_pending') : 0;
                                    @endphp
                                    <option value="{{ $c->id }}" data-debt="{{ $debt }}" data-ci="{{ $c->ci }}" data-phone="{{ $c->phone }}">
                                        {{ $c->name }} (CI: {{ $c->ci }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7 d-flex justify-content-around mt-2 mt-md-0 border-left">
                                <div class="text-center">
                                    <span class="text-muted small d-block">Deuda Actual en Créditos:</span>
                                    <span class="h5 font-weight-bold text-danger mb-0" id="lblClientDebt">Bs. 0.00</span>
                                </div>
                                <div class="text-center">
                                    <span class="text-muted small d-block">Teléfono / Celular:</span>
                                    <span class="h6 font-weight-bold text-dark mb-0" id="lblClientPhone">-</span>
                                </div>
                                <div class="text-center">
                                    <span class="text-muted small d-block">Evaluación:</span>
                                    <span class="badge badge-success p-2 mt-1" id="lblCreditBadge">
                                        <i class="fas fa-check-circle"></i> Apto para Crédito
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. AGREGAR PRODUCTOS A LA VENTA -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-dark mb-3">
                            <i class="fas fa-boxes text-primary mr-1"></i> Productos a Financiar
                        </h5>

                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Producto:</label>
                                <select id="product_selector" class="form-control" onchange="onProductSelected()">
                                    <option value="" disabled selected>Seleccione un producto</option>
                                    @foreach($products as $p)
                                    @php
                                        $isUnlimited = $p->isService() || (($activeCompany->business_type ?? '') === 'RESTAURANTE' && $p->stock <= 0);
                                        $saleStock = $isUnlimited ? 999999 : $p->stock;
                                    @endphp
                                    <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}" data-stock="{{ $saleStock }}">
                                        {{ $p->name }} (Bs. {{ number_format($p->sell_price, 2) }} - {{ $isUnlimited ? 'Plato/Servicio' : 'Stock: ' . $p->stock }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-weight-bold">Precio (Bs.):</label>
                                <input type="number" id="item_price" class="form-control" readonly>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small font-weight-bold">Cantidad:</label>
                                <input type="number" id="item_qty" class="form-control" value="1" min="1">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm float-right mb-3" onclick="addProductToCreditSale()">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>

                        <div class="table-responsive w-100">
                            <table class="table table-striped table-bordered" id="creditDetailsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Acción</th>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center text-muted py-3">No hay productos agregados.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. CONDICIONES DE FINANCIAMIENTO Y CRONOGRAMA -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-dark mb-3">
                            <i class="fas fa-calculator text-success mr-1"></i> Términos de Financiamiento
                        </h5>

                        <div class="p-3 bg-light rounded mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Total de la Venta:</span>
                                <span class="h5 font-weight-bold text-dark mb-0" id="lblSaleTotal">Bs. 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Cuota Inicial (Al contado):</span>
                                <span class="font-weight-bold text-success" id="lblDownPaymentSummary">Bs. 0.00</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold text-danger">Monto a Financiar (Saldo):</span>
                                <span class="h5 font-weight-bold text-danger mb-0" id="lblBalanceToFinance">Bs. 0.00</span>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Cuota Inicial (Bs.):</label>
                            <input type="number" step="0.5" name="down_payment" id="down_payment" class="form-control" value="0.00" min="0" onchange="calculateInstallments()" onkeyup="calculateInstallments()">
                        </div>

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Forma de Pago (Cuota Inicial):</label>
                            <select name="payment_method_id" id="payment_method_id" class="form-control form-control-sm">
                                @foreach($paymentMethods->whereIn('payment_condition', ['CONTADO', 'AMBOS']) as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="col-6 form-group">
                                <label class="small font-weight-bold">Frecuencia:</label>
                                <select name="installment_frequency" id="installment_frequency" class="form-control" onchange="calculateInstallments()">
                                    <option value="MENSUAL" selected>Mensual</option>
                                    <option value="QUINCENAL">Quincenal</option>
                                    <option value="SEMANAL">Semanal</option>
                                </select>
                            </div>
                            <div class="col-6 form-group">
                                <label class="small font-weight-bold">N° de Cuotas:</label>
                                <select name="installments_count" id="installments_count" class="form-control" onchange="calculateInstallments()">
                                    <option value="1">1 Cuota</option>
                                    <option value="2">2 Cuotas</option>
                                    <option value="3" selected>3 Cuotas</option>
                                    <option value="6">6 Cuotas</option>
                                    <option value="12">12 Cuotas</option>
                                    <option value="18">18 Cuotas</option>
                                    <option value="24">24 Cuotas</option>
                                </select>
                            </div>
                        </div>

                        <!-- VISTA PREVIA DEL CRONOGRAMA -->
                        <h6 class="font-weight-bold text-dark mt-3 mb-2 small text-uppercase">
                            <i class="fas fa-calendar-alt text-info mr-1"></i> Cronograma Proyectado de Cuotas:
                        </h6>
                        <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Cuota</th>
                                        <th>Vencimiento</th>
                                        <th>Monto (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody id="installmentsPreviewBody">
                                    <tr>
                                        <td colspan="3" class="text-center text-muted small">Agregue productos para ver cuotas.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" id="btnConfirmCreditSale" class="btn btn-info btn-block btn-lg font-weight-bold mt-3" onclick="submitCreditSale()" disabled>
                            <i class="fas fa-check-circle mr-1"></i> CONFIRMAR VENTA A CRÉDITO
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
    let creditCart = [];

    function onClientSelected() {
        let opt = $('#client_id').find(':selected');
        let debt = parseFloat(opt.data('debt')) || 0;
        let phone = opt.data('phone') || 'No registrado';

        $('#lblClientDebt').text('Bs. ' + debt.toFixed(2));
        $('#lblClientPhone').text(phone);

        if (debt > 5000) {
            $('#lblCreditBadge').removeClass('badge-success').addClass('badge-warning').html('<i class="fas fa-exclamation-triangle"></i> Deuda Alta');
        } else {
            $('#lblCreditBadge').removeClass('badge-warning').addClass('badge-success').html('<i class="fas fa-check-circle"></i> Apto para Crédito');
        }
    }

    function onProductSelected() {
        let opt = $('#product_selector').find(':selected');
        let price = opt.data('price') || 0;
        $('#item_price').val(parseFloat(price).toFixed(2));
    }

    function addProductToCreditSale() {
        let opt = $('#product_selector').find(':selected');
        let id = opt.val();
        if (!id) {
            Swal.fire('Atención', 'Seleccione un producto.', 'warning');
            return;
        }

        let name = opt.text().split('(')[0].trim();
        let price = parseFloat(opt.data('price')) || 0;
        let stock = parseInt(opt.data('stock')) || 0;
        let qty = parseInt($('#item_qty').val()) || 1;

        if (qty <= 0) {
            Swal.fire('Atención', 'La cantidad debe ser mayor a 0.', 'warning');
            return;
        }

        let existing = creditCart.find(i => i.id == id);
        let currentQty = existing ? existing.qty : 0;
        if (currentQty + qty > stock) {
            Swal.fire('Stock Insuficiente', 'Solo hay ' + stock + ' unidades disponibles.', 'warning');
            return;
        }

        if (existing) {
            existing.qty += qty;
        } else {
            creditCart.push({ id, name, price, qty });
        }

        $('#item_qty').val(1);
        renderCreditTable();
    }

    function removeProductFromCredit(id) {
        creditCart = creditCart.filter(i => i.id != id);
        renderCreditTable();
    }

    function renderCreditTable() {
        let tbody = $('#creditDetailsTable tbody');
        tbody.empty();

        if (creditCart.length === 0) {
            tbody.append('<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-3">No hay productos agregados.</td></tr>');
            $('#btnConfirmCreditSale').prop('disabled', true);
            calculateInstallments();
            return;
        }

        $('#btnConfirmCreditSale').prop('disabled', false);

        creditCart.forEach(item => {
            let sub = item.qty * item.price;
            let row = `
                <tr>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-xs" onclick="removeProductFromCredit(${item.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                    <td>${item.name}</td>
                    <td>Bs. ${item.price.toFixed(2)}</td>
                    <td>${item.qty}</td>
                    <td class="font-weight-bold">Bs. ${sub.toFixed(2)}</td>

                    <input type="hidden" name="product_id[]" value="${item.id}">
                    <input type="hidden" name="quantity[]" value="${item.qty}">
                    <input type="hidden" name="price[]" value="${item.price}">
                    <input type="hidden" name="discount[]" value="0">
                </tr>
            `;
            tbody.append(row);
        });

        calculateInstallments();
    }

    function calculateInstallments() {
        let total = creditCart.reduce((acc, i) => acc + (i.qty * i.price), 0);
        let downPayment = parseFloat($('#down_payment').val()) || 0;

        if (downPayment > total) {
            downPayment = total;
            $('#down_payment').val(total.toFixed(2));
        }

        let balance = Math.max(0, total - downPayment);
        let count = parseInt($('#installments_count').val()) || 1;
        let freq = $('#installment_frequency').val();

        $('#lblSaleTotal').text('Bs. ' + total.toFixed(2));
        $('#lblDownPaymentSummary').text('Bs. ' + downPayment.toFixed(2));
        $('#lblBalanceToFinance').text('Bs. ' + balance.toFixed(2));
        $('#total_pagar').val(total.toFixed(2));

        let previewBody = $('#installmentsPreviewBody');
        previewBody.empty();

        if (balance <= 0) {
            previewBody.append('<tr><td colspan="3" class="text-center text-success small py-2">Financiamiento cubierto en su totalidad con la cuota inicial.</td></tr>');
            return;
        }

        let perInstallment = (balance / count).toFixed(2);
        let date = new Date();

        for (let i = 1; i <= count; i++) {
            let dueDate = new Date(date);
            if (freq === 'SEMANAL') {
                dueDate.setDate(dueDate.getDate() + (i * 7));
            } else if (freq === 'QUINCENAL') {
                dueDate.setDate(dueDate.getDate() + (i * 15));
            } else {
                dueDate.setMonth(dueDate.getMonth() + i);
            }

            let dateStr = dueDate.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
            previewBody.append(`
                <tr>
                    <td class="font-weight-bold">Cuota #${i}</td>
                    <td>${dateStr}</td>
                    <td class="font-weight-bold text-dark">Bs. ${perInstallment}</td>
                </tr>
            `);
        }
    }

    function submitCreditSale() {
        let clientId = $('#client_id').val();
        if (!clientId) {
            Swal.fire('Atención', 'Debe seleccionar un cliente para otorgar el crédito.', 'warning');
            return;
        }

        if (creditCart.length === 0) {
            Swal.fire('Atención', 'Debe agregar al menos un producto a financiar.', 'warning');
            return;
        }

        let total = parseFloat($('#total_pagar').val()) || 0;
        let count = $('#installments_count').val();

        let btn = document.getElementById('btnConfirmCreditSale');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> PROCESANDO CRÉDITO...';
        document.getElementById('creditSaleForm').submit();
    }
</script>
@endsection
