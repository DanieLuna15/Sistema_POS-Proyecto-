@extends('layouts.admin')
@section('title', 'Detalle de Arqueo de Caja #' . $cashRegister->id)

@section('content')
@php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Detalle de Turno y Arqueo de Caja #{{ str_pad($cashRegister->id, 5, '0', STR_PAD_LEFT) }}
            </h3>
            <small class="text-muted">Sucursal: {{ $cashRegister->branch->name ?? $activeBranch->name ?? 'Casa Matriz' }} &bull; Cajero: {{ $cashRegister->user->name }}</small>
        </div>
        <div>
            <a href="{{ route('cash_registers.pdf', $cashRegister) }}" target="_blank" class="btn btn-danger mr-2">
                <i class="fas fa-file-pdf mr-1"></i> Imprimir PDF de Arqueo
            </a>
            <a href="{{ route('cash_registers.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-1"></i> Volver a Cajas
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Resumen Financiero del Turno</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th>Estado del Turno:</th>
                            <td>
                                <span class="badge {{ $cashRegister->status === 'ABIERTA' ? 'badge-success' : 'badge-dark' }}">
                                    {{ $cashRegister->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Apertura:</th>
                            <td>{{ \Carbon\Carbon::parse($cashRegister->opening_date)->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Cierre:</th>
                            <td>{{ $cashRegister->closing_date ? \Carbon\Carbon::parse($cashRegister->closing_date)->format('d/m/Y H:i:s') : 'Turno Abierto' }}</td>
                        </tr>
                        <tr>
                            <th>Monto Inicial en Gaveta:</th>
                            <td class="text-right font-weight-bold">{{ $sym }} {{ number_format($cashRegister->initial_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>(+) Ventas en Efectivo:</th>
                            <td class="text-right text-success font-weight-bold">+{{ $sym }} {{ number_format($cashRegister->sales_cash, 2) }}</td>
                        </tr>
                        <tr>
                            <th>(+) Cobros de Cuotas Crédito:</th>
                            <td class="text-right text-info font-weight-bold">+{{ $sym }} {{ number_format($cashRegister->credit_collections, 2) }}</td>
                        </tr>
                        <tr>
                            <th>(+) Otros Ingresos de Caja:</th>
                            <td class="text-right text-primary font-weight-bold">+{{ $sym }} {{ number_format($cashRegister->incomes_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>(-) Gastos / Egresos de Caja Chica:</th>
                            <td class="text-right text-danger font-weight-bold">-{{ $sym }} {{ number_format($cashRegister->expenses_amount, 2) }}</td>
                        </tr>
                        <tr class="bg-light">
                            <th>SALDO ESPERADO EN EFECTIVO:</th>
                            <td class="text-right font-weight-bold text-primary" style="font-size: 16px;">{{ $sym }} {{ number_format($cashRegister->expected_amount, 2) }}</td>
                        </tr>
                        @if($cashRegister->counted_amount !== null)
                        <tr>
                            <th>EFECTIVO CONTADO (ARQUEO):</th>
                            <td class="text-right font-weight-bold" style="font-size: 16px;">{{ $sym }} {{ number_format($cashRegister->counted_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>DIFERENCIA (SOBRANTE / FALTANTE):</th>
                            <td class="text-right font-weight-bold {{ $cashRegister->difference < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $sym }} {{ number_format($cashRegister->difference, 2) }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Ventas Digitales (QR / Tarjeta / Transf.):</th>
                            <td class="text-right font-weight-bold">{{ $sym }} {{ number_format($cashRegister->sales_other, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Detalle de Ingresos y Egresos del Turno</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Tipo</th>
                                    <th>Categoría</th>
                                    <th>Descripción</th>
                                    <th class="text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cashRegister->movements as $m)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($m->movement_date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $m->type === 'INGRESO' ? 'badge-success' : 'badge-danger' }}">{{ $m->type }}</span>
                                    </td>
                                    <td>{{ $m->category }}</td>
                                    <td>{{ $m->description }}</td>
                                    <td class="text-right font-weight-bold">
                                        {{ $sym }} {{ number_format($m->amount, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Sin movimientos manuales de caja chica en este turno.</td>
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

