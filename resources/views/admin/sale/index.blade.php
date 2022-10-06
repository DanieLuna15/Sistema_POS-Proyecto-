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
<li class="nav-item d-none d-lg-flex">
    <a class="nav-link" href="{{route('sales.create')}}">
        <span class="btn btn-primary">+ Nueva Venta</span>
    </a>
</li>
@endsection

@section('options')
<li class="nav-item nav-settings d-none d-lg-block">
    <a class="nav-link" href="#">
        <i class="fas fa-ellipsis-h"></i>
    </a>
</li>
@endsection
@section('preference')
    <div id="right-sidebar" class="settings-panel">
        <i class="settings-close fa fa-times"></i>
        <ul class="nav nav-tabs" id="setting-panel" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">Gestión de Ventas</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                    <a class="nav-link" href="{{route('sales.create')}}">
                        <span class="btn btn-primary">+ Registrar Venta</span>
                    </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
    </div>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
           Gestión de Ventas
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
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

                        <div class="dropdown">
                          <button type="button" class="btn btn-dark dropdown-toggle" id="dropdownMenuIconButton7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuIconButton7">
                            <h6 class="dropdown-header">Acciones</h6>
                            <a class="dropdown-item" href="{{route('sales.create')}}">Nueva Venta +</a>
                            <a class="dropdown-item" href="#">Ver Historial</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Exportar a PDF</a>
                            <a class="dropdown-item" href="#">Exportar a Excel</a>
                          </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                <!-- 'client_id',
                                'user_id',
                                'sale_date',
                                'tax',
                                'total',
                                'status',  -->

                                    <th>Id</th>
                                    <th>Fecha y Hora</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th style="width:50px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <th scope="row">
                                        <a href="{{route('sales.show', $sale)}}">{{$sale->id}}</a>
                                    </th>
                                        <td>{{$sale->sale_date}}</td>
                                        <td>{{$sale->client->name}}</td>
                                        <td>Bs./ {{$sale->total}}</td>

                                        <!--<td>
                                            @if ($sale->status=='CONFIRMADO')
                                                <button class="btn btn-success btn-block ">{{$sale->status}}</button>
                                            @else
                                                <button class="btn btn-danger btn-block ">{{$sale->status}}</button>

                                                @if ($sale->status=='CANCELADO')

                                                @else ($sale->status=='PENDIENTE')
                                                    <button class="btn btn-warning btn-block ">{{$sale->status}}</button>
                                                @endif
                                            @endif
                                        </td>-->
                                        <td>
                                            @if ($sale->status=='CONFIRMADO')
                                                <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.sales', $sale)}}">
                                                    {{$sale->status}} <i class="fas fa-check"></i>
                                                </a>
                                            @else
                                                <a class="jsgrid-button btn btn-danger btn-sm btn-block disabled" href="{{route('change.status.sales', $sale)}}">
                                                    {{$sale->status}} <i class="fas fa-times"></i>
                                                </a>
                                            @endif
                                        </td>

                                        <td style="width:50px;">


                                        <a href="#" class="jsgrid-button jsgrid-edit-button"><i class="far fa-file-pdf"></i></a>
                                        <a href="#" class="jsgrid-button jsgrid-edit-button"><i class="fas fa-print"></i></a>
                                        <a href="{{route('sales.show', $sale)}}" class="jsgrid-button jsgrid-edit-button"><i class="far fa-eye"></i></a>

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
