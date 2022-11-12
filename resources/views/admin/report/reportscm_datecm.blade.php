@extends('layouts.admin')
@section('title','Reportes de Compras')
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
            Reporte de compras
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reporte de Compras</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="rango-tab" data-toggle="tab" href="#rango-1" role="tab" aria-controls="rango-1" aria-selected="true">Rango de Fechas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="diaria-tab" data-toggle="tab" href="#diaria-1" role="tab" aria-controls="diaria-1" aria-selected="false">Reporte del dia</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="mensual-tab" data-toggle="tab" href="#mensual-1" role="tab" aria-controls="mensual-1" aria-selected="false">Reporte del mes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="anual-tab" data-toggle="tab" href="#anual-1" role="tab" aria-controls="anual-1" aria-selected="false">Reporte del año</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="proveedores-tab" data-toggle="tab" href="#proveedores-1" role="tab" aria-controls="proveedores-1" aria-selected="false">Proveedores más Rentables</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="canceladas-tab" data-toggle="tab" href="#canceladas-1" role="tab" aria-controls="canceladas-1" aria-selected="false">Compras Canceladas</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="rango-1" role="tabpanel" aria-labelledby="rango-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title">Reporte Personalizado:</h4>
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
                                    {!! Form::open(['route'=>'reportcm.resultscm', 'method'=>'POST']) !!}
                                    <div class="row ">
                                        <div class="col-12 col-md-4">
                                            <span>Fecha Inicial:</span>
                                            <div class="form-group">
                                                <input class="form-control" type="date" value="{{old('fecha_fin',date('Y-m-d'))}}" name="fecha_ini" id="fecha_ini">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <span>Fecha Final:</span>
                                            <div class="form-group">
                                                <input class="form-control" type="date" value="{{old('fecha_fin',date('Y-m-d'))}}" name="fecha_fin" id="fecha_fin">
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-4 text-center mt-4">
                                            <div class="form-group">
                                               <button type="submit" class="btn btn-primary btn-md">Generar Reporte</button>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                    <div class="row ">
                                        <div class="col-12 col-md-3 text-center">
                                            <span>Fecha Inicio: <b></b></span>
                                            <div class="form-group">
                                                <strong>{{ Carbon\Carbon::parse($fi)->format('d/m/Y') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 text-center">
                                            <span>Fecha Fin: <b> </b></span>
                                            <div class="form-group">
                                                <strong>{{ Carbon\Carbon::parse($ff)->format('d/m/Y') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 text-center">
                                            <span>Cantidad de registros: <b></b></span>
                                            <div class="form-group">
                                                <strong>{{$cantcompras}}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3 text-center">
                                            <span>Total de Gastos: <b> </b></span>
                                            <div class="form-group">
                                                <strong>Bs./ {{$total}}.00</strong>
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
                                                    <th>Acciones</th>
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
                        <div class="tab-pane fade" id="diaria-1" role="tabpanel" aria-labelledby="diaria-tab">
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
                                            <span>Cantidad de Compras: <b></b></span>
                                            <div class="form-group">
                                                <strong>{{$cantcomprasd}}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 text-center">
                                            <span>Total en Gastos: <b> </b></span>
                                            <div class="form-group">
                                                <strong>Bs./ {{$totald}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="order-listing1" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Proveedor</th>
                                                    <th>Total /Bs.</th>
                                                    <th>Fecha y Hora</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($purchasesd as $purchased): ?>
                                                <tr>
                                                    <th scope="row">
                                                        <a href="{{route('purchases.show', $purchased)}}">{{$purchased->id}}</a>
                                                    </th>
                                                    <td>{{$purchased->provider->name}}</td>
                                                    <td>{{$purchased->total}}</td>
                                                    <td>{{ Carbon\Carbon::parse($purchased->purchase_date)->format('d/m/Y H:i:s') }}</td>

                                                    <td style="width: 10%;">
                                                        @if ($purchased->status=='CONFIRMADO')
                                                            <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.purchases', $purchased)}}">
                                                                {{$purchased->status}} <i class="fas fa-check"></i>
                                                            </a>
                                                        @else
                                                            <a class="jsgrid-button btn btn-danger btn-sm btn-block disabled" href="{{route('change.status.purchases', $purchased)}}">
                                                                {{$purchased->status}} <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td style="width: 20%;" align="center">
                                                        <a href="{{route('purchases.pdf', $purchased)}}" class="btn btn-outline-danger"
                                                        title="Generar PDF"><i class="far fa-file-pdf"></i></a>
                                                        <a href="#" class="btn btn-outline-warning"
                                                        title="Imprimir boleta"><i class="fas fa-print"></i></a>
                                                        <a href="{{route('purchases.show', $purchased)}}" class="btn btn-outline-info"
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
                        <div class="tab-pane fade" id="mensual-1" role="tabpanel" aria-labelledby="mensual-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title">Todas las Compras del Mes:</h4>
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
                                            <span>Mes de consulta: <b> </b></span>
                                            <div class="form-group">
                                                <strong>{{\Carbon\Carbon::now('America/La_Paz')->formatLocalized('%B')}}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 text-center">
                                            <span>Cantidad de Compras: <b></b></span>
                                            <div class="form-group">
                                                <strong>{{$cantcomprasm}}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 text-center">
                                            <span>Total en Gastos: <b> </b></span>
                                            <div class="form-group">
                                                <strong>Bs./ {{$totalm}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="order-listing2" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Proveedor</th>
                                                    <th>Total /Bs.</th>
                                                    <th>Fecha y Hora</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($purchasesm as $purchasem): ?>
                                                <tr>
                                                    <th scope="row">
                                                        <a href="{{route('purchases.show', $purchasem)}}">{{$purchasem->id}}</a>
                                                    </th>
                                                    <td>{{$purchasem->provider->name}}</td>
                                                    <td>{{$purchasem->total}}</td>
                                                    <td>{{ Carbon\Carbon::parse($purchasem->purchase_date)->format('d/m/Y H:i:s') }}</td>

                                                    <td style="width: 10%;">
                                                        @if ($purchasem->status=='CONFIRMADO')
                                                            <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.purchases', $purchasem)}}">
                                                                {{$purchasem->status}} <i class="fas fa-check"></i>
                                                            </a>
                                                        @else
                                                            <a class="jsgrid-button btn btn-danger btn-sm btn-block disabled" href="{{route('change.status.purchases', $purchasem)}}">
                                                                {{$purchasem->status}} <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td style="width: 20%;" align="center">
                                                        <a href="{{route('purchases.pdf', $purchasem)}}" class="btn btn-outline-danger"
                                                        title="Generar PDF"><i class="far fa-file-pdf"></i></a>
                                                        <a href="#" class="btn btn-outline-warning"
                                                        title="Imprimir boleta"><i class="fas fa-print"></i></a>
                                                        <a href="{{route('purchases.show', $purchasem)}}" class="btn btn-outline-info"
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
                        <div class="tab-pane fade" id="anual-1" role="tabpanel" aria-labelledby="anual-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title">Todas las Compras del Año:</h4>
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
                                            <span>Año de consulta: <b> </b></span>
                                            <div class="form-group">
                                                <strong>{{\Carbon\Carbon::now('America/La_Paz')->formatLocalized('%Y')}}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 text-center">
                                            <span>Cantidad de Compras: <b></b></span>
                                            <div class="form-group">
                                                <strong>{{$cantcomprasy}}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 text-center">
                                            <span>Total en Gastos: <b> </b></span>
                                            <div class="form-group">
                                                <strong>Bs./ {{$totaly}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="order-listing3" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Proveedor</th>
                                                    <th>Total /Bs.</th>
                                                    <th>Fecha y Hora</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($purchasesy as $purchasey): ?>
                                                <tr>
                                                    <th scope="row">
                                                        <a href="{{route('purchases.show', $purchasey)}}">{{$purchasey->id}}</a>
                                                    </th>
                                                    <td>{{$purchasey->provider->name}}</td>
                                                    <td>{{$purchasey->total}}</td>
                                                    <td>{{ Carbon\Carbon::parse($purchasey->purchase_date)->format('d/m/Y H:i:s') }}</td>

                                                    <td style="width: 10%;">
                                                        @if ($purchasey->status=='CONFIRMADO')
                                                            <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.purchases', $purchasey)}}">
                                                                {{$purchasey->status}} <i class="fas fa-check"></i>
                                                            </a>
                                                        @else
                                                            <a class="jsgrid-button btn btn-danger btn-sm btn-block disabled" href="{{route('change.status.purchases', $purchasey)}}">
                                                                {{$purchasey->status}} <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td style="width: 20%;" align="center">
                                                        <a href="{{route('purchases.pdf', $purchasey)}}" class="btn btn-outline-danger"
                                                        title="Generar PDF"><i class="far fa-file-pdf"></i></a>
                                                        <a href="#" class="btn btn-outline-warning"
                                                        title="Imprimir boleta"><i class="fas fa-print"></i></a>
                                                        <a href="{{route('purchases.show', $purchasey)}}" class="btn btn-outline-info"
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
                        <div class="tab-pane fade" id="proveedores-1" role="tabpanel" aria-labelledby="proveedores-tab">
                            <div class="row">
                                <div class="col-lg-4 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h4 class="card-title">
                                                    <i class="fas fa-table"></i>
                                                    Top 6 Provedores más rentables: <strong>{{\Carbon\Carbon::now('America/La_Paz')->formatLocalized('%Y')}}</strong>
                                                </h4>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr align="center">
                                                            <th>Nombre</th>
                                                            <th>Cantidad de Compras</th>
                                                            <th>Cantidad de Productos Abastecidos</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($mejoresprovcantcompras as $mejorprovcantcompras)
                                                        <tr>
                                                            <td>{{$mejorprovcantcompras->nameprov}}</td>
                                                            <td  align="center">
                                                                <a class="jsgrid-button btn btn-success btn-sm btn-rounded">
                                                                    <strong>{{$mejorprovcantcompras->quantity}}</strong>
                                                                </a>
                                                            </td>
                                                            <td  align="center">
                                                                <a class="jsgrid-button btn btn-info btn-sm btn-rounded">
                                                                    <strong>{{$mejorprovcantcompras->quantityprod}}</strong>
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
                                <div class="col-md-4 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body d-flex flex-column">
                                            <h4 class="card-title">
                                                <i class="fas fa-tachometer-alt"></i>
                                                Cantidad de productos por proveedor
                                            </h4>
                                                <p class="card-description">Este es un top de los proveedores con mas productos abastecidos</p>
                                            <div class="flex-grow-1 d-flex flex-column justify-content-between">
                                                <canvas id="daily-sales-chart" class="mt-3 mb-3 mb-md-0"></canvas>
                                                <div id="daily-sales-chart-legend" class="daily-sales-chart-legend pt-4 border-top"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body d-flex flex-column">
                                            <h4 class="card-title">
                                                <i class="fas fa-chart-pie"></i>
                                                Cantidad de Compras por Proveedor
                                            </h4>
                                            <p class="card-description">Este es un top de los proveedores con más compras realizadas</p>
                                            <div class="flex-grow-1 d-flex flex-column justify-content-between">
                                                <canvas id="sales-status-chart" class="mt-3"></canvas>
                                                <div class="pt-4">
                                                    <div id="sales-status-chart-legend" class="sales-status-chart-legend"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="canceladas-1" role="tabpanel" aria-labelledby="canceladas-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title">Todas las Compras Canceladas del año:</h4>
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
                                            <span>Año de consulta: <b> </b></span>
                                            <div class="form-group">
                                                <strong>{{\Carbon\Carbon::now('America/La_Paz')->formatLocalized('%Y')}}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 text-center">
                                            <span>Cantidad de Compras Canceladas: <b></b></span>
                                            <div class="form-group">
                                                <strong>{{$cantcomprascanc}}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 text-center">
                                            <span>Monto Total: <b> </b></span>
                                            <div class="form-group">
                                                <strong>Bs./ {{$totalcanc}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="order-listing4" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Proveedor</th>
                                                    <th>Total /Bs.</th>
                                                    <th>Fecha y Hora</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($purchasescanc as $purchasecanc): ?>
                                                <tr>
                                                    <th scope="row">
                                                        <a href="{{route('purchases.show', $purchasecanc)}}">{{$purchasecanc->id}}</a>
                                                    </th>
                                                    <td>{{$purchasecanc->provider->name}}</td>
                                                    <td>{{$purchasecanc->total}}</td>
                                                    <td>{{ Carbon\Carbon::parse($purchasecanc->purchase_date)->format('d/m/Y H:i:s') }}</td>

                                                    <td style="width: 10%;">
                                                        @if ($purchasecanc->status=='CONFIRMADO')
                                                            <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.purchases', $purchasecanc)}}">
                                                                {{$purchasecanc->status}} <i class="fas fa-check"></i>
                                                            </a>
                                                        @else
                                                            <a class="jsgrid-button btn btn-danger btn-sm btn-block disabled" href="{{route('change.status.purchases', $purchasecanc)}}">
                                                                {{$purchasecanc->status}} <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td style="width: 20%;" align="center">
                                                        <a href="{{route('purchases.pdf', $purchasecanc)}}" class="btn btn-outline-danger"
                                                        title="Generar PDF"><i class="far fa-file-pdf"></i></a>
                                                        <a href="#" class="btn btn-outline-warning"
                                                        title="Imprimir boleta"><i class="fas fa-print"></i></a>
                                                        <a href="{{route('purchases.show', $purchasecanc)}}" class="btn btn-outline-info"
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
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}

<script>
    window.onload = function(){
        var fecha = new Date(); //Fecha actual
        var mes = fecha.getMonth()+1; //obteniendo mes
        var dia = fecha.getDate(); //obteniendo dia
        var ano = fecha.getFullYear(); //obteniendo año
        if(dia<10)
          dia='0'+dia; //agrega cero si es menor de 10
        if(mes<10)
          mes='0'+mes //agrega cero si es menor de 10
        document.getElementById('fecha_fin').value=ano+"-"+mes+"-"+dia;
        document.getElementById('fecha_fin').max=ano+"-"+mes+"-"+dia;
        document.getElementById('fecha_ini').max=ano+"-"+mes+"-"+dia;
      }
</script>
<script>
    $(function(){
        if ($("#daily-sales-chart").length) {
            var dailySalesChartData = {
                datasets: [{
                    data: [<?php foreach ($mejoresprovcantproductos as $mejorprovcantcompras)
                    {echo ''. $mejorprovcantcompras->quantityprod.',';}  ?>],
                backgroundColor: [
                    '#3498DB',
                    '#FF5733',
                    '#A04000',
                    '#FFC300',
                    '#58D68D',
                    '#6C3483'
                ],
                borderWidth: 0
                }],
                labels: [
                <?php foreach($mejoresprovcantproductos as $mejorprovcantcompras):?>
                    "<?php echo $mejorprovcantcompras->nameprov?>",
                <?php endforeach; ?>
            ],
            };
            var dailySalesChartOptions = {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                animateScale: true,
                animateRotate: true
                },
                legend: {
                display: false
                },
                legendCallback: function(chart) {
                var text = [];
                text.push('<ul align="center" class="legend'+ chart.id +'">');
                for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                    text.push('<li><span  class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                    if (chart.data.labels[i]) {
                    text.push(chart.data.labels[i]);
                    }
                    text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto" >'+ chart.data.datasets[0].data[i] + ' Unidades. </label>');
                    text.push('</li>');
                }
                text.push('</ul>');
                return text.join("");
                },
                cutoutPercentage: 70
            };
            var dailySalesChartCanvas = $("#daily-sales-chart").get(0).getContext("2d");
            var dailySalesChart = new Chart(dailySalesChartCanvas, {
                type: 'doughnut',
                data: dailySalesChartData,
                options: dailySalesChartOptions
            });
            document.getElementById('daily-sales-chart-legend').innerHTML = dailySalesChart.generateLegend();
        }
    });
</script>
<script>
    $(function(){
        if ($("#sales-status-chart").length) {
        var pieChartCanvas = $("#sales-status-chart").get(0).getContext("2d");
        var pieChart = new Chart(pieChartCanvas, {
            type: 'pie',
            data: {
            datasets: [{
                data: [<?php foreach ($mejoresprovcantcompras as $mejorprovcantcompras)
                    {echo ''. $mejorprovcantcompras->quantity.',';}  ?>],
                backgroundColor: [
                    '#3498DB',
                    '#FF5733',
                    '#A04000',
                    '#FFC300',
                    '#58D68D',
                    '#6C3483'
                ],
                borderColor: [
                    '#3498DB',
                    '#FF5733',
                    '#A04000',
                    '#FFC300',
                    '#58D68D',
                    '#6C3483'
                ],
            }],
            labels: [
                <?php foreach($mejoresprovcantcompras as $mejorprovcantcompras):?>
                    "<?php echo $mejorprovcantcompras->nameprov?>",
                <?php endforeach; ?>
            ],
            },
            options: {
            responsive: true,
            animation: {
                animateScale: true,
                animateRotate: true
            },
            legend: {
                display: false
            },
            legendCallback: function(chart) {
                var text = [];
                text.push('<ul class="legend'+ chart.id +'">');
                for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                if (chart.data.labels[i]) {
                    text.push(chart.data.labels[i]);
                }
                text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">'+ chart.data.datasets[0].data[i] + ' Compra(s)</label>');
                text.push('</li>');
                }
                text.push('</ul>');
                return text.join("");
            }
            }
        });
        document.getElementById('sales-status-chart-legend').innerHTML = pieChart.generateLegend();
        }
    });
</script>

@endsection
