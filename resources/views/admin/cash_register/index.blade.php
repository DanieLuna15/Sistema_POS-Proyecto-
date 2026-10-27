@extends('layouts.admin')
@section('title', 'Control de Caja, Turnos y Gastos Operativos')

@section('content')
@php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-cash-register text-success mr-2"></i> Control de Caja, Arqueo y Gastos Chicos
            </h3>
            <small class="text-muted">Sucursal: <strong>{{ $activeBranch->name ?? 'Casa Matriz' }}</strong> &bull; Apertura de turnos, ingresos, gastos operativos y cuadre de caja.</small>
        </div>
        <div>
            <button type="button" class="btn btn-outline-success mr-1" data-toggle="modal" data-target="#incomeModal">
                <i class="fas fa-plus-circle"></i> Registrar Ingreso
            </button>
            <button type="button" class="btn btn-outline-danger mr-1" data-toggle="modal" data-target="#expenseModal">
                <i class="fas fa-minus-circle"></i> Registrar Gasto / Egreso
            </button>
            @if(!$openRegister)
                <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#openCashModal">
                    <i class="fas fa-lock-open mr-1"></i> Abrir Turno de Caja
                </button>
            @else
                <button type="button" class="btn btn-warning text-dark font-weight-bold" data-toggle="modal" data-target="#closeCashModal">
                    <i class="fas fa-calculator mr-1"></i> Arquear y Cerrar Caja
                </button>
            @endif
        </div>
    </div>

    <!-- PANEL DEL TURNO DE CAJA ACTUAL -->
    @if($openRegister)
    <div class="card border-success mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div>
                    <span class="badge badge-success badge-pill px-3 py-1 mb-1">
                        <i class="fas fa-circle mr-1"></i> TURNO DE CAJA ABIERTO (#{{ str_pad($openRegister->id, 4, '0', STR_PAD_LEFT) }})
                    </span>
                    <h4 class="mb-0 mt-1">Responsable: {{ $openRegister->user->name }} &bull; Apertura: {{ \Carbon\Carbon::parse($openRegister->opening_date)->format('d/m/Y H:i') }}</h4>
                </div>
                <div class="text-right">
                    <small class="text-muted d-block">SALDO ESPERADO EN EFECTIVO EN GAVETA</small>
                    <h2 class="text-success font-weight-bold mb-0">{{ $sym }} {{ number_format($openRegister->expected_amount, 2) }}</h2>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-md-2 col-6 mb-2">
                    <div class="p-2 bg-light rounded border">
                        <small class="text-muted d-block">Monto Inicial</small>
                        <strong class="text-dark" style="font-size: 16px;">{{ $sym }} {{ number_format($openRegister->initial_amount, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <div class="p-2 bg-light rounded border">
                        <small class="text-muted d-block">(+) Ventas Efectivo</small>
                        <strong class="text-success" style="font-size: 16px;">{{ $sym }} {{ number_format($openRegister->sales_cash, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <div class="p-2 bg-light rounded border">
                        <small class="text-muted d-block">(+) Cobros Crédito</small>
                        <strong class="text-info" style="font-size: 16px;">{{ $sym }} {{ number_format($openRegister->credit_collections, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <div class="p-2 bg-light rounded border">
                        <small class="text-muted d-block">(+) Otros Ingresos</small>
                        <strong class="text-primary" style="font-size: 16px;">{{ $sym }} {{ number_format($openRegister->incomes_amount, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <div class="p-2 bg-light rounded border">
                        <small class="text-muted d-block">(-) Gastos / Egresos</small>
                        <strong class="text-danger" style="font-size: 16px;">-{{ $sym }} {{ number_format($openRegister->expenses_amount, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <div class="p-2 bg-light rounded border">
                        <small class="text-muted d-block">Ventas QR / Tarjeta</small>
                        <strong class="text-secondary" style="font-size: 16px;">{{ $sym }} {{ number_format($openRegister->sales_other, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1"><i class="fas fa-lock mr-2"></i> No hay ningún turno de caja abierto en este momento</h5>
            <span>Abra un turno indicando el efectivo inicial en gaveta para llevar el control exacto del arqueo diario.</span>
        </div>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#openCashModal">
            <i class="fas fa-lock-open mr-1"></i> Abrir Caja Ahora
        </button>
    </div>
    @endif

    <div class="row">
        <!-- MOVIMIENTOS DE CAJA CHICA (INGRESOS Y GASTOS OPERATIVOS) -->
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-exchange-alt text-primary mr-1"></i> Movimientos de Caja Chica y Gastos Operativos</h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Categoría / Concepto</th>
                                    <th class="text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $m)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($m->movement_date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($m->type === 'INGRESO')
                                            <span class="badge badge-success">INGRESO</span>
                                        @else
                                            <span class="badge badge-danger">EGRESO</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $m->category }}</strong><br>
                                        <small class="text-muted">{{ $m->description }} ({{ $m->user->name ?? '' }})</small>
                                    </td>
                                    <td class="text-right font-weight-bold {{ $m->type === 'INGRESO' ? 'text-success' : 'text-danger' }}">
                                        {{ $m->type === 'INGRESO' ? '+' : '-' }}{{ $sym }} {{ number_format($m->amount, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No se registraron movimientos de caja chica todavía.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIAL DE ARQUEOS Y TURNOS DE CAJA -->
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-history text-info mr-1"></i> Historial de Turnos y Arqueos de Caja</h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Apertura / Cierre</th>
                                    <th>Cajero</th>
                                    <th>Esperado</th>
                                    <th>Arqueado</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($registers as $reg)
                                <tr>
                                    <td>#{{ $reg->id }}</td>
                                    <td>
                                        <small>
                                            <strong>A:</strong> {{ \Carbon\Carbon::parse($reg->opening_date)->format('d/m H:i') }}<br>
                                            <strong>C:</strong> {{ $reg->closing_date ? \Carbon\Carbon::parse($reg->closing_date)->format('d/m H:i') : 'En curso' }}
                                        </small>
                                    </td>
                                    <td>{{ $reg->user->name ?? '' }}</td>
                                    <td class="font-weight-bold">{{ $sym }} {{ number_format($reg->expected_amount, 2) }}</td>
                                    <td>
                                        @if($reg->counted_amount !== null)
                                            {{ $sym }} {{ number_format($reg->counted_amount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($reg->status === 'ABIERTA')
                                            <span class="badge badge-success">ABIERTA</span>
                                        @else
                                            <span class="badge badge-secondary">CERRADA</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('cash_registers.show', $reg) }}" class="btn btn-outline-info btn-sm" title="Ver Arqueo">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('cash_registers.pdf', $reg) }}" target="_blank" class="btn btn-outline-danger btn-sm" title="PDF Arqueo">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Sin turnos registrados aún.</td>
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

<!-- Modal Abrir Caja -->
<div class="modal fade" id="openCashModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('cash_registers.open') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-lock-open mr-2"></i> Apertura de Turno de Caja</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Efectivo Inicial en Caja ({{ $sym }}) *</label>
                        <input type="number" step="0.01" min="0" name="initial_amount" class="form-control form-control-lg font-weight-bold" value="0.00" required>
                        <small class="text-muted">Monto de cambio/sencillo con el que inicia el turno.</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Observaciones de Apertura</label>
                        <textarea name="opening_notes" class="form-control" rows="2" placeholder="Ej: Turno mañana, billetes para cambio verificados..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Abrir Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Ingreso -->
<div class="modal fade" id="incomeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('cash_registers.movements.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="INGRESO">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i> Registrar Ingreso Extra a Caja</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Categoría de Ingreso *</label>
                        <select name="category" class="form-control" required>
                            <option value="APORTE_CAJA">Aporte de Cambio / Sencillo</option>
                            <option value="INGRESO_SERVICIO">Ingreso por Servicio Extra</option>
                            <option value="OTRO_INGRESO">Otro Ingreso</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Monto ({{ $sym }}) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Descripción / Motivo *</label>
                        <textarea name="description" class="form-control" rows="2" required placeholder="Detalle del ingreso..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Ingreso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Gasto / Egreso Operativo -->
<div class="modal fade" id="expenseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('cash_registers.movements.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="EGRESO">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-minus-circle mr-2"></i> Registrar Gasto Operativo / Egreso de Caja Chica</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Categoría del Gasto *</label>
                        <select name="category" class="form-control" required>
                            <option value="GASTO_OPERATIVO">Gasto Operativo de Tienda / Insumos</option>
                            <option value="SERVICIOS_BASICOS">Servicios Básicos (Luz, Agua, Internet)</option>
                            <option value="TRANSPORTE">Transporte / Flete / Delivery</option>
                            <option value="ALIMENTACION">Alimentación / Refrigerio Personal</option>
                            <option value="PAGO_PROVEEDOR">Pago Menor a Proveedor</option>
                            <option value="ADELANTO_PERSONAL">Adelanto / Sueldo Personal</option>
                            <option value="RETIRO_CAJA">Retiro de Efectivo a Bóveda</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Monto ({{ $sym }}) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">N° Recibo / Comprobante (Opcional)</label>
                        <input type="text" name="reference" class="form-control" placeholder="Ej: Recibo #104">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Detalle del Gasto *</label>
                        <textarea name="description" class="form-control" rows="2" required placeholder="Explique en qué se utilizó el dinero..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Registrar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Arqueo y Cierre de Caja -->
@if($openRegister)
<div class="modal fade" id="closeCashModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('cash_registers.close', $openRegister) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-calculator mr-2"></i> Arqueo y Cierre de Turno de Caja #{{ $openRegister->id }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-primary mb-3">Calculadora de Billetes y Monedas (Opcional)</h6>
                            <div class="row">
                                @foreach([200, 100, 50, 20, 10, 5, 2, 1, 0.50] as $denom)
                                <div class="col-6 mb-2 d-flex align-items-center">
                                    <span style="width: 65px; font-weight: bold;">{{ $sym }} {{ $denom }}:</span>
                                    <input type="number" min="0" class="form-control form-control-sm bill-input" data-denom="{{ $denom }}" placeholder="Cant." oninput="calcCashCount()">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded border mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Efectivo Esperado por Sistema:</span>
                                    <strong id="expected_display" data-val="{{ $openRegister->expected_amount }}">{{ $sym }} {{ number_format($openRegister->expected_amount, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Ventas QR / Tarjeta / Otros:</span>
                                    <strong>{{ $sym }} {{ number_format($openRegister->sales_other, 2) }}</strong>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Efectivo Físico Contado en Gaveta ({{ $sym }}) *</label>
                                <input type="number" step="0.01" min="0" name="counted_amount" id="counted_amount"
                                       class="form-control form-control-lg font-weight-bold text-primary"
                                       value="{{ number_format($openRegister->expected_amount, 2, '.', '') }}"
                                       oninput="updateDiff()" required>
                            </div>

                            <div id="diff_alert" class="alert alert-success py-2">
                                <strong>Cuadre Exacto:</strong> Diferencia {{ $sym }} 0.00
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Observaciones de Cierre</label>
                                <textarea name="closing_notes" class="form-control" rows="2" placeholder="Observaciones o novedades del turno..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning font-weight-bold text-dark">
                        <i class="fas fa-lock mr-1"></i> Confirmar Arqueo y Cerrar Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function calcCashCount() {
        var total = 0;
        document.querySelectorAll('.bill-input').forEach(function(inp) {
            var qty = parseFloat(inp.value) || 0;
            var denom = parseFloat(inp.getAttribute('data-denom')) || 0;
            total += (qty * denom);
        });
        var countedInput = document.getElementById('counted_amount');
        if (countedInput) {
            countedInput.value = total.toFixed(2);
            updateDiff();
        }
    }

    function updateDiff() {
        var expectedEl = document.getElementById('expected_display');
        var countedEl = document.getElementById('counted_amount');
        var alertEl = document.getElementById('diff_alert');
        if (!expectedEl || !countedEl || !alertEl) return;

        var expected = parseFloat(expectedEl.getAttribute('data-val')) || 0;
        var counted = parseFloat(countedEl.value) || 0;
        var diff = counted - expected;

        if (Math.abs(diff) < 0.01) {
            alertEl.className = 'alert alert-success py-2';
            alertEl.innerHTML = '<strong><i class="fas fa-check-circle"></i> Cuadre Exacto:</strong> Sin faltantes ni sobrantes (0.00)';
        } else if (diff > 0) {
            alertEl.className = 'alert alert-info py-2';
            alertEl.innerHTML = '<strong><i class="fas fa-arrow-up"></i> Sobrante en Caja:</strong> +' + diff.toFixed(2);
        } else {
            alertEl.className = 'alert alert-danger py-2';
            alertEl.innerHTML = '<strong><i class="fas fa-exclamation-triangle"></i> Faltante en Caja:</strong> ' + diff.toFixed(2);
        }
    }
</script>
@endsection

