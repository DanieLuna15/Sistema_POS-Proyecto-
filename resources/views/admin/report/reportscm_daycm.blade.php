@extends('layouts.admin')
@section('title','Reporte de Ventas del Día')
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
            Reporte de Compras del Día
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reporte de Compras del Día</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <!--<div class="col-md-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home-1" role="tab" aria-controls="home-1" aria-selected="true">Ventas del dia</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile-1" role="tab" aria-controls="profile-1" aria-selected="false">Rango de Fechas</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact-1" role="tab" aria-controls="contact-1" aria-selected="false">Gráficos</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="home-1" role="tabpanel" aria-labelledby="home-tab">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title">Todas las Ventas del día:</h4>

                                <div class="dropdown">
                                  <button type="button" class="btn btn-light dropdown-toggle" id="dropdownMenuIconButton7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-cog"></i>
                                  </button>
                                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuIconButton7">
                                    <h6 class="dropdown-header">Acciones</h6>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Exportar a PDF</a>
                                    <a class="dropdown-item" href="#">Exportar a Excel</a>
                                  </div>
                                </div>
                            </div>

                            <div class="row ">
                                <div class="col-12 col-md-4 text-center">
                                    <span>Fecha de consulta: <b> </b></span>
                                    <div class="form-group">
                                        <strong>{{\Carbon\Carbon::now('America/La_Paz')->format('d/m/Y')}}</strong>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 text-center">
                                    <span>Cantidad de registros: <b></b></span>
                                    <div class="form-group">
                                        <strong>{{$cantcompras}}</strong>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 text-center">
                                    <span>Total de ingresos: <b> </b></span>
                                    <div class="form-group">
                                        <strong>Bs./ {{$totalcm}}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="order-listing" class="table">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Fecha y Hora</th>
                                            <th>Cliente</th>
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

                                                <td>
                                                    @if ($purchase->status=='CONFIRMADO')
                                                        <button class="btn btn-success btn-block ">{{$purchase->status}}</button>
                                                    @else
                                                        <button class="btn btn-danger btn-block ">{{$purchase->status}}</button>

                                                        @if ($purchase->status=='CANCELADO')

                                                        @else ($purchase->status=='PENDIENTE')
                                                            <button class="btn btn-warning btn-block ">{{$purchase->status}}</button>
                                                        @endif
                                                    @endif
                                                </td>
                                                <!--<td>
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
                  <div class="tab-pane fade" id="profile-1" role="tabpanel" aria-labelledby="profile-tab">

                  </div>
                  <div class="tab-pane fade" id="contact-1" role="tabpanel" aria-labelledby="contact-tab">
                    <h1>Acá van los gráficos</h1>
                  </div>
                </div>
              </div>
            </div>
        </div>-->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Todas las Compras del día:</h4>

                        <div class="dropdown">
                          <button type="button" class="btn btn-light dropdown-toggle" id="dropdownMenuIconButton7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuIconButton7">
                             <a class="dropdown-item" href="#">Exportar a PDF</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Exportar a Excel</a>
                          </div>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-12 col-md-4 text-center">
                            <span>Fecha de consulta: <b> </b></span>
                            <div class="form-group">
                                <strong>{{\Carbon\Carbon::now('America/La_Paz')->format('d/m/Y')}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-center">
                            <span>Cantidad de registros: <b></b></span>
                            <div class="form-group">
                                <strong>{{$cantcompras}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-center">
                            <span>Total de Gastos: <b> </b></span>
                            <div class="form-group">
                                <strong>Bs./ {{$totalcm}}.00</strong>
                            </div>
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>

                                    <th>Id</th>
                                    <th>Proveedor</th>
                                    <th>Total /Bs.</th>
                                    <th>Fecha y Hora</th>
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
                                    <td>{{$purchase->provider->name}}</td>
                                    <td>{{$purchase->total}}</td>
                                    <td>{{ Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y H:i:s') }}</td>

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

                                    <td style="width: 10%;">
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

                                    <td style="width: 20%;" align="center">
                                        <a href="{{route('purchases.pdf', $purchase)}}" class="btn btn-outline-danger"
                                        title="Generar PDF"><i class="far fa-file-pdf"></i></a>
                                        <a href="#" class="btn btn-outline-warning"
                                        title="Imprimir boleta"><i class="fas fa-print"></i></a>
                                        <a href="{{route('purchases.show', $purchase)}}" class="btn btn-outline-info"
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
@endsection
@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
@endsection
