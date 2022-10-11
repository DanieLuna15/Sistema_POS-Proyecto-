@extends('layouts.admin')
@section('title','Reporte por rango de Fechas')
<!--Reporte de Ventas-->
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
            Reporte por rango de Fechas
            <!--Reporte de Ventas-->
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reporte por rango de Fechas<!--Reporte de Ventas--></li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Reporte Personalizado:</h4>
                        <div class="dropdown">
                          <button type="button" class="btn btn-dark dropdown-toggle" id="dropdownMenuIconButton7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    {!! Form::open(['route'=>'report.results', 'method'=>'POST']) !!}
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
                               <button type="submit" class="btn btn-primary btn-md">Consultar</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <div class="row ">
                        <div class="col-12 col-md-3 text-center">
                            <span>Fecha Inicio: <b></b></span>
                            <div class="form-group">
                                <strong>{{$fi}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Fecha Fin: <b> </b></span>
                            <div class="form-group">
                                <strong>{{$ff}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Cantidad de registros: <b></b></span>
                            <div class="form-group">
                                <strong>{{$cantventas}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Total de ingresos: <b> </b></span>
                            <div class="form-group">
                                <strong>Bs./ {{$total}}</strong>
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
                                            <a href="{{route('sales.pdf', $sale)}}" class="jsgrid-button jsgrid-edit-button"><i class="far fa-file-pdf"></i></a>
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
@endsection
