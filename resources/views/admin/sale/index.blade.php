@extends('layouts.admin')
@section('title','Gestión de Ventas')
@section('styles')
<style type="text/css">
    .unstyled-button {
        border: none;
        padding: 0;
        background: none;
      }
</style>
@endsection

@section('create')
@endsection

@section('options')
@endsection

@section('preference')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
           Gestión de Ventas
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ventas</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Todas las Ventas:</h4>
                        <div class="btn-group">
                            <a href="{{route('sales.pos')}}" class="btn btn-warning font-weight-bold">
                                <i class="fas fa-cash-register mr-1"></i> Caja Rápida (POS)
                            </a>
                            <a href="{{route('sales.credit.create')}}" class="btn btn-info font-weight-bold">
                                <i class="fas fa-hand-holding-usd mr-1"></i> Venta a Crédito
                            </a>
                            <a href="{{route('sales.create')}}" class="btn btn-primary font-weight-bold">
                                <i class="fas fa-plus mr-1"></i> Venta Tradicional
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Cliente</th>
                                    <th>Tipo / Pago</th>
                                    <th>Total /Bs.</th>
                                    <th>Fecha y Hora</th>
                                    <th>Estado</th>
                                    <th style="width:120px;" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <th scope="row">
                                        <a href="{{route('sales.show', $sale)}}">#{{$sale->id}}</a>
                                    </th>
                                    <td>{{$sale->client->name ?? 'N/A'}}</td>
                                    <td>
                                        @if(($sale->payment_condition ?? $sale->sale_type) === 'CREDITO')
                                            <span class="badge badge-info"><i class="fas fa-hand-holding-usd"></i> CRÉDITO</span>
                                        @elseif($sale->sale_type === 'POS_RAPIDO')
                                            <span class="badge badge-warning text-dark"><i class="fas fa-bolt"></i> POS RÁPIDO</span>
                                        @else
                                            <span class="badge badge-success"><i class="fas fa-money-bill-wave"></i> CONTADO</span>
                                        @endif
                                        <small class="d-block text-dark font-weight-bold">{{$sale->payment_method ?? 'EFECTIVO'}}</small>
                                    </td>
                                    <td class="font-weight-bold">Bs. {{number_format($sale->total, 2)}}</td>
                                    <td>{{ Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($sale->status=='CONFIRMADO')
                                            <a class="jsgrid-button btn btn-success btn-sm btn-block" title="Deshabilitar" href="{{route('change.status.sales', $sale)}}">
                                                {{$sale->status}} <i class="fas fa-check"></i>
                                            </a>
                                        @else
                                            <a class="jsgrid-button btn btn-danger btn-sm btn-block disabled" href="{{route('change.status.sales', $sale)}}">
                                                {{$sale->status}} <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <a href="{{route('sales.ticket', $sale)}}" target="_blank" class="btn btn-outline-warning btn-sm"
                                        title="Imprimir Ticket Térmico 80mm"><i class="fas fa-receipt"></i></a>
                                        <a href="{{route('sales.pdf', $sale)}}" target="_blank" class="btn btn-outline-danger btn-sm"
                                        title="Ver Nota de Venta A4 (PDF)"><i class="far fa-file-pdf"></i></a>
                                        <a href="{{route('sales.show', $sale)}}" class="btn btn-outline-info btn-sm"
                                        title="Ver detalles de venta"><i class="far fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
