@extends('layouts.admin')
@section('title','Gestión de Compras')
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
        Gestión de Compras
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Compras</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Todas las Compras:</h4>
                        <div class="btn-group">
                            <a href="{{route('purchases.create')}}" type="button" class="btn btn-info ">
                                <i class="fas fa-plus"></i> Nuevo
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>

                                    <th>Id</th>
                                    <th>Fecha y Hora</th>
                                    <th>Proveedor</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th style="width:50px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchases as $purchase): ?>
                                <tr>
                                    <th scope="row">
                                        <a href="{{route('purchases.show', $purchase)}}">{{$purchase->id}}</a>
                                    </th>
                                        <td>{{$purchase->purchase_date}}</td>
                                        <td>{{$purchase->provider->name}}</td>
                                        <td>Bs./ {{$purchase->total}}</td>

                                        <!--<td>
                                            @if ($purchase->status=='CONFIRMADO')
                                                <button class="btn btn-success btn-block ">{{$purchase->status}}</button>
                                            @else
                                                <button class="btn btn-danger btn-block ">{{$purchase->status}}</button>

                                                @if ($purchase->status=='CANCELADO')

                                                @else ($purchase->status=='PENDIENTE')
                                                    <button class="btn btn-warning btn-block ">{{$purchase->status}}</button>
                                                @endif
                                            @endif
                                        </td>-->

                                        <td>
                                            @if ($purchase->status=='CONFIRMADO')
                                                <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.purchases', $purchase)}}">
                                                    {{$purchase->status}} <i class="fas fa-check"></i>
                                                </a>
                                            @else
                                                <a class="jsgrid-button btn btn-danger btn-sm btn-block disabled" href="{{route('change.status.purchases', $purchase)}}">
                                                    {{$purchase->status}} <i class="fas fa-times"></i>
                                                </a>
                                            @endif
                                        </td>

                                        <td style="width:50px;">


                                        <a href="{{route('purchases.pdf', $purchase)}}" class="jsgrid-button jsgrid-edit-button"><i class="far fa-file-pdf"></i></a>
                                        <a href="#" class="jsgrid-button jsgrid-edit-button"><i class="fas fa-print"></i></a>
                                        <a href="{{route('purchases.show', $purchase)}}" class="jsgrid-button jsgrid-edit-button"><i class="far fa-eye"></i></a>

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
