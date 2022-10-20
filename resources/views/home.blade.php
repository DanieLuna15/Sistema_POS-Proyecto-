@extends('layouts.admin')
@section('title','Dashboard')
@section('styles')
<style type="text/css">
    .unstyled-button {
        border: none;
        padding: 0;
        background: none;
      }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Dashboard
        </h3>
    </div>
    <div class="row grid-margin">
        <div class="col-12">
          <div class="card card-statistics">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                    <div class="statistics-item">
                        <p>
                        <i class="icon-sm fa fa-user mr-1"></i>
                        Total Clientes
                        </p>
                        <h2>{{$cantclientesTotal}}</h2>
                        @if ($cantclientesHoy==0)
                            <label class="badge badge-outline-danger badge-pill">{{$cantclientesHoy}} Nuevos hoy.</label>
                        @else
                            <label class="badge badge-outline-success badge-pill">{{$cantclientesHoy}} Nuevos hoy.</label>
                        @endif
                    </div>
                    <div class="statistics-item">
                        <p>
                        <i class="icon-sm fas fa-shipping-fast mr-1"></i>
                        Total Proveedores
                        </p>
                        <h2>{{$cantprovsTotal}}</h2>
                        @if ($cantprovsHoy==0)
                            <label class="badge badge-outline-danger badge-pill">{{$cantprovsHoy}} Nuevos hoy.</label>
                        @else
                            <label class="badge badge-outline-success badge-pill">{{$cantprovsHoy}} Nuevos hoy.</label>
                        @endif
                    </div>
                    <div class="statistics-item">
                        <p>
                        <i class="icon-sm fas fa-bullhorn mr-1"></i>
                        Total Monto en Ventas
                        </p>
                        <h2>{{$totalvn}} Bs.</h2>
                        @if ($totalvn>$totalcm)
                            <label class="badge badge-outline-success badge-pill">{{$totalcm}} Bs. Monto en Compras</label>
                        @else
                            <label class="badge badge-outline-warning badge-pill">{{$totalcm}} Bs. Monto en Compras</label>
                        @endif

                    </div>

                    <div class="statistics-item">
                        <p>
                        <i class="icon-sm fas fa-shopping-cart mr-1"></i>
                        Total Ventas
                        </p>
                        <h2>{{$cantventasTotal}}</h2>
                        @if ($cantventasHoy==0)
                            <label class="badge badge-outline-danger badge-pill">{{$cantventasHoy}} Realizadas hoy.</label>
                        @else
                            <label class="badge badge-outline-success badge-pill">{{$cantventasHoy}} Realizadas hoy.</label>
                        @endif
                    </div>
                    <div class="statistics-item">
                        <p>
                        <i class="icon-sm fas fa-cart-plus mr-1"></i>
                        Total Compras
                        <h2>{{$cantcomprasTotal}}</h2>
                        @if ($cantcomprasHoy==0)
                            <label class="badge badge-outline-danger badge-pill">{{$cantcomprasHoy}} Realizadas hoy.</label>
                        @else
                            <label class="badge badge-outline-success badge-pill">{{$cantcomprasHoy}} Realizadas hoy.</label>
                        @endif
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
            <div class="card-body">
                    <h4 class="card-title">
                        <i class="fas fa-gift"></i>
                        Compras vs Ventas (GRAFICO 1)
                    </h4>
                    <canvas id="orders-chart"></canvas>
                <div id="orders-chart-legend" class="orders-chart-legend"></div>
            </div>
        </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Ventas del Mes (GRAFICO 2)
                    </h4>
                    <h2 class="mb-5">56000 <span class="text-muted h4 font-weight-normal">Ventas</span></h2>
                    <canvas id="sales-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="fas fa-table"></i>
                        Top 10 de Productos más vendidos
                    </h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th>Nombre</th>
                                    <th>Código</th>
                                    <th>Stock Actual</th>
                                    <th>Cantidad vendida</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productosmasvendidos as $productosmasvendido)
                                <tr>
                                    <td>{{$productosmasvendido->id}}</td>
                                    <td>{{$productosmasvendido->name}}</td>
                                    <td>{{$productosmasvendido->code}}</td>
                                    <td><strong>{{$productosmasvendido->stock}}</strong> Unidades</td>
                                    <td><strong>{{$productosmasvendido->quantity}}</strong> Unidades</td>

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
{!! Html::script('melody/js/chart.js') !!}

<!-- endinject -->
<!-- Custom js for this page-->
{!! Html::script('melody/js/dashboard.js') !!}


{!! Html::script('melody/js/vendor.bundle.base.js') !!}
{!! Html::script('melody/js/vendor.bundle.addons.js') !!}
<!-- endinject -->
<!-- Plugin js for this page-->
<!-- End plugin js for this page-->
<!-- inject:js -->
<!-- endinject -->

@endsection
