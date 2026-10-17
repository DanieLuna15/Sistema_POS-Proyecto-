@extends('layouts.admin')
@section('title', 'Cuentas por Cobrar - Ventas a Crédito')

@section('content')
<div class="content-wrapper">
    <div class="page-header mb-3">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-hand-holding-usd text-info mr-2"></i> Cuentas por Cobrar (Ventas a Crédito)
            </h3>
            <span class="text-muted">Control de financiamientos, cobro de cuotas y seguimiento de saldos.</span>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Cuentas por Cobrar</li>
            </ol>
        </nav>
    </div>

    <!-- TARJETAS DE ESTADÍSTICAS FINANCIERAS -->
    <div class="row mb-3">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #4b49ac !important;">
                <div class="card-body py-3">
                    <span class="text-muted small text-uppercase font-weight-bold">Total Créditos Otorgados</span>
                    <h3 class="font-weight-bold text-dark mt-2 mb-0">Bs. {{ number_format($totalCredito, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #dc3545 !important;">
                <div class="card-body py-3">
                    <span class="text-muted small text-uppercase font-weight-bold text-danger">Saldo Pendiente por Cobrar</span>
                    <h3 class="font-weight-bold text-danger mt-2 mb-0">Bs. {{ number_format($totalPendiente, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #28a745 !important;">
                <div class="card-body py-3">
                    <span class="text-muted small text-uppercase font-weight-bold text-success">Total Cobrado / Recuperado</span>
                    <h3 class="font-weight-bold text-success mt-2 mb-0">Bs. {{ number_format($totalCobrado, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Listado de Ventas a Crédito:</h4>
                        <a href="{{ route('sales.credit.create') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-plus"></i> Nueva Venta a Crédito
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th># Venta</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total (Bs.)</th>
                                    <th>Cuota Inicial</th>
                                    <th>Saldo Pendiente</th>
                                    <th>Cuotas</th>
                                    <th>Estado Crédito</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                @php
                                    $paidInstallments = $sale->installments->where('status', 'PAGADO')->count();
                                    $totalInstallments = $sale->installments->count();
                                @endphp
                                <tr>
                                    <td class="font-weight-bold">#{{ $sale->id }}</td>
                                    <td>
                                        <span class="font-weight-bold text-dark d-block">{{ $sale->client->name ?? 'N/A' }}</span>
                                        <small class="text-muted">CI: {{ $sale->client->ci ?? '-' }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                    <td class="font-weight-bold">Bs. {{ number_format($sale->total, 2) }}</td>
                                    <td>Bs. {{ number_format($sale->down_payment, 2) }}</td>
                                    <td>
                                        @if($sale->balance_pending > 0)
                                            <span class="font-weight-bold text-danger">Bs. {{ number_format($sale->balance_pending, 2) }}</span>
                                        @else
                                            <span class="text-success font-weight-bold">Bs. 0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">
                                            {{ $paidInstallments }} / {{ $totalInstallments }} pagadas
                                        </span>
                                    </td>
                                    <td>
                                        @if($sale->credit_status === 'PAGADO')
                                            <span class="badge badge-success"><i class="fas fa-check"></i> PAGADO</span>
                                        @else
                                            <span class="badge badge-warning text-dark"><i class="fas fa-clock"></i> PENDIENTE</span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <button type="button" class="btn btn-outline-info btn-sm mr-1" 
                                                data-toggle="modal" data-target="#modalInstallments{{ $sale->id }}" 
                                                title="Gestionar Cuotas y Pagos">
                                            <i class="fas fa-calendar-check"></i> Cuotas
                                        </button>
                                        <a href="{{ route('sales.ticket', $sale) }}" target="_blank" class="btn btn-outline-warning btn-sm mr-1" title="Imprimir Ticket 80mm">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-outline-danger btn-sm mr-1" title="Ver Nota A4">
                                            <i class="far fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary btn-sm" title="Ver Detalle Completo">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- MODAL DE GESTIÓN DE CUOTAS PARA ESTA VENTA -->
                                <div class="modal fade" id="modalInstallments{{ $sale->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title font-weight-bold">
                                                    <i class="fas fa-calendar-alt text-info mr-2"></i> Cronograma de Cuotas - Venta #{{ $sale->id }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3 bg-light p-2 rounded mx-0">
                                                    <div class="col-md-4">
                                                        <span class="text-muted small d-block">Cliente:</span>
                                                        <strong class="text-dark">{{ $sale->client->name ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="text-muted small d-block">Total Crédito:</span>
                                                        <strong class="text-dark">Bs. {{ number_format($sale->total, 2) }}</strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="text-muted small d-block">Saldo Pendiente:</span>
                                                        <strong class="text-danger">Bs. {{ number_format($sale->balance_pending, 2) }}</strong>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm text-center">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Cuota #</th>
                                                                <th>Fecha Vencimiento</th>
                                                                <th>Monto Cuota</th>
                                                                <th>Monto Pagado</th>
                                                                <th>Estado</th>
                                                                <th>Acción / Pago</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($sale->installments as $inst)
                                                            <tr>
                                                                <td class="font-weight-bold">Cuota #{{ $inst->installment_number }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d/m/Y') }}</td>
                                                                <td>Bs. {{ number_format($inst->amount, 2) }}</td>
                                                                <td>Bs. {{ number_format($inst->paid_amount, 2) }}</td>
                                                                <td>
                                                                    @if($inst->status === 'PAGADO')
                                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Pagado</span>
                                                                    @else
                                                                        <span class="badge badge-warning text-dark"><i class="fas fa-clock"></i> Pendiente</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($inst->status !== 'PAGADO')
                                                                    <form action="{{ route('sales.installments.pay', $inst) }}" method="POST" class="d-inline-flex align-items-center">
                                                                        @csrf
                                                                        <select name="payment_method_id" class="form-control form-control-xs mr-1" style="height: 28px; font-size: 11px; padding: 2px 4px; width: 130px;">
                                                                            @foreach($paymentMethods->whereIn('payment_condition', ['CONTADO', 'AMBOS']) as $pm)
                                                                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <input type="hidden" name="paid_amount" value="{{ $inst->amount - $inst->paid_amount }}">
                                                                        <button type="submit" class="btn btn-success btn-xs text-nowrap" onclick="return confirm('¿Registrar cobro de Bs. {{ number_format($inst->amount - $inst->paid_amount, 2) }} para esta cuota?')">
                                                                            <i class="fas fa-check"></i> Cobrar
                                                                        </button>
                                                                    </form>
                                                                    @else
                                                                    <span class="small text-muted d-block">
                                                                        {{ $inst->paymentMethod->name ?? $inst->payment_method ?? 'Efectivo' }}
                                                                    </span>
                                                                    <span class="small text-success font-weight-bold">
                                                                        {{ $inst->payment_date ? \Carbon\Carbon::parse($inst->payment_date)->format('d/m/Y') : 'Pagado' }}
                                                                    </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-between">
                                                <div>
                                                    <a href="{{ route('sales.ticket', $sale) }}" target="_blank" class="btn btn-warning btn-sm font-weight-bold mr-1">
                                                        <i class="fas fa-receipt mr-1"></i> Ticket 80mm
                                                    </a>
                                                    <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-danger btn-sm font-weight-bold">
                                                        <i class="fas fa-file-pdf mr-1"></i> Nota A4 con Cuotas
                                                    </a>
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 text-muted"></i>
                                        <p class="mb-0">No hay ventas a crédito registradas en el sistema.</p>
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
@endsection
