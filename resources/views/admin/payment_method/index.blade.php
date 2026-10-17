@extends('layouts.admin')
@section('title', 'Formas y Métodos de Pago')

@section('styles')
<style>
    .kpi-card {
        border-radius: 8px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .method-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-right: 10px;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <i class="fas fa-money-check-alt text-primary mr-2"></i> Formas y Métodos de Pago
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Formas de Pago</li>
            </ol>
        </nav>
    </div>

    <!-- TARJETAS DE ESTADÍSTICAS -->
    <div class="row mb-4">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card kpi-card bg-gradient-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white">Total Formas</h6>
                            <h3 class="mb-0 font-weight-bold">{{ $totalMethods }}</h3>
                        </div>
                        <i class="fas fa-credit-card fa-2x opacity-7"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card kpi-card bg-gradient-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white">Pago al Contado</h6>
                            <h3 class="mb-0 font-weight-bold">{{ $totalContado }}</h3>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x opacity-7"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card kpi-card bg-gradient-warning text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white">Pago a Crédito</h6>
                            <h3 class="mb-0 font-weight-bold">{{ $totalCredito }}</h3>
                        </div>
                        <i class="fas fa-hand-holding-usd fa-2x opacity-7"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card kpi-card bg-gradient-info text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white">Métodos Activos</h6>
                            <h3 class="mb-0 font-weight-bold">{{ $totalActivos }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA PRINCIPAL DE FORMAS DE PAGO -->
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="card-title mb-1">Catálogo de Formas de Pago</h4>
                            <p class="card-description mb-0">
                                Configure los métodos de pago disponibles para ventas al contado, caja rápida y crédito.
                            </p>
                        </div>
                        @can('payment_methods.create')
                        <a href="{{ route('payment_methods.create') }}" class="btn btn-primary font-weight-bold">
                            <i class="fas fa-plus mr-1"></i> Nueva Forma de Pago
                        </a>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th>Forma de Pago</th>
                                    <th>Código</th>
                                    <th>Condición</th>
                                    <th>Referencia</th>
                                    <th>Ventas Asoc.</th>
                                    <th>Estado</th>
                                    <th class="text-center" style="width: 15%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paymentMethods as $index => $pm)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="method-icon-box" style="background-color: {{ $pm->color }}20; color: {{ $pm->color }};">
                                                <i class="{{ $pm->icon ?? 'fas fa-money-bill' }}"></i>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold text-dark d-block">{{ $pm->name }}</span>
                                                @if($pm->description)
                                                    <small class="text-muted d-block text-truncate" style="max-width: 280px;" title="{{ $pm->description }}">
                                                        {{ $pm->description }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="font-weight-bold text-primary">{{ $pm->code }}</code>
                                    </td>
                                    <td>
                                        {!! $pm->condition_badge !!}
                                    </td>
                                    <td>
                                        @if($pm->requires_reference)
                                            <span class="badge badge-light border text-info font-weight-bold" title="Requiere ingresar N° de comprobante / voucher">
                                                <i class="fas fa-receipt mr-1"></i> Obligatorio
                                            </span>
                                        @else
                                            <span class="text-muted small">No requerida</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary font-weight-bold">
                                            {{ $pm->sales_count }} ventas
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('change.status.payment_methods', $pm) }}" 
                                           class="btn btn-xs {{ $pm->status === 'ACTIVO' ? 'btn-success' : 'btn-danger' }}"
                                           title="Clic para cambiar estado">
                                            <i class="fas {{ $pm->status === 'ACTIVO' ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                            {{ $pm->status }}
                                        </a>
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        @can('payment_methods.edit')
                                        <a href="{{ route('payment_methods.edit', $pm) }}" class="btn btn-outline-info btn-sm" title="Editar">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('payment_methods.destroy')
                                        @if($pm->sales_count == 0)
                                        <form action="{{ route('payment_methods.destroy', $pm) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta forma de pago?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @else
                                        <button class="btn btn-outline-secondary btn-sm disabled" title="No eliminable porque tiene ventas registradas" disabled>
                                            <i class="fas fa-lock"></i>
                                        </button>
                                        @endif
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
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
