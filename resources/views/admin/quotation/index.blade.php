@extends('layouts.admin')
@section('title', 'Cotizaciones y Proformas')

@section('content')
@php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-file-contract text-primary mr-2"></i> Gestión de Cotizaciones y Proformas
            </h3>
            <small class="text-muted">Genera presupuestos en PDF sin descontar stock y conviértelos a Venta Confirmada en 1 clic.</small>
        </div>
        <a href="{{ route('quotations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle mr-1"></i> Nueva Cotización / Proforma
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="order-listing" class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Proforma</th>
                            <th>Fecha Emisión</th>
                            <th>Válida Hasta</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $q)
                        <tr>
                            <td class="font-weight-bold text-primary">#COT-{{ str_pad($q->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ \Carbon\Carbon::parse($q->quotation_date)->format('d/m/Y H:i') }}</td>
                            <td>{{ $q->valid_until ? \Carbon\Carbon::parse($q->valid_until)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $q->client->name ?? 'S/N' }}</td>
                            <td>{{ $q->user->name ?? '' }}</td>
                            <td class="font-weight-bold">{{ $sym }} {{ number_format($q->total, 2) }}</td>
                            <td>
                                @if($q->status === 'CONVERTIDA')
                                    <span class="badge badge-success">CONVERTIDA EN VENTA #{{ $q->converted_sale_id }}</span>
                                @elseif($q->status === 'PENDIENTE')
                                    <span class="badge badge-warning">PENDIENTE</span>
                                @else
                                    <span class="badge badge-secondary">{{ $q->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('quotations.show', $q) }}" class="btn btn-outline-info btn-sm" title="Ver y Convertir">
                                    <i class="fas fa-eye"></i> Detalle
                                </a>
                                <a href="{{ route('quotations.pdf', $q) }}" target="_blank" class="btn btn-outline-danger btn-sm" title="Imprimir PDF">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
@endsection

