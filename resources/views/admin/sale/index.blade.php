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
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ventas</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Todas las Ventas:</h4>
                        <div class="btn-group">
                            <a href="{{route('sales.create')}}" type="button" class="btn btn-info ">
                                <i class="fas fa-plus"></i> Nuevo
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Cliente</th>
                                    <th>Total /Bs.</th>
                                    <th>Fecha y Hora</th>
                                    <th>Estado</th>
                                    <th style="width:100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <th scope="row">
                                        <a href="{{route('sales.show', $sale)}}">{{$sale->id}}</a>
                                    </th>
                                    <td>{{$sale->client->name}}</td>
                                    <td>{{$sale->total}}</td>
                                    <td>{{ Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i:s') }}</td>
                                    <td style="width: 10%;">
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
                                    <td style="width: 20%;">
                                        <a href="{{route('sales.pdf', $sale)}}" class="btn btn-outline-danger"
                                        title="Generar PDF"><i class="far fa-file-pdf"></i></a>
                                        <a href="" class="btn btn-outline-warning"
                                        title="Imprimir boleta"><i class="fas fa-print"></i></a>
                                        <a href="{{route('sales.show', $sale)}}" class="btn btn-outline-info"
                                        title="Ver detalles"><i class="far fa-eye"></i></a>
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
