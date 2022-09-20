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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="col-12">
                            <div class="card card-statistics">
                                <div class="card-body">
                                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                                        <div class="statistics-item">
                                            <p>
                                            <i class="icon-sm fa fa-user mr-2"></i>
                                            Total Clientes
                                            </p>
                                            <h2>50</h2>
                                            <label class="badge badge-outline-success badge-pill">2 nuevos hoy</label>
                                        </div>
                                        <div class="statistics-item">
                                            <p>
                                            <i class="icon-sm fas fa-hourglass-half mr-2"></i>
                                            Total Proveedores
                                            </p>
                                            <h2>15</h2>
                                            <label class="badge badge-outline-danger badge-pill">10 nuevos hoy</label>
                                        </div>
                                        <!--
                                        <div class="statistics-item">
                                            <p>
                                            <i class="icon-sm fas fa-cloud-download-alt mr-2"></i>
                                            Downloads
                                            </p>
                                            <h2>3500</h2>
                                            <label class="badge badge-outline-success badge-pill">12% increase</label>
                                        </div>
                                        <div class="statistics-item">
                                            <p>
                                            <i class="icon-sm fas fa-check-circle mr-2"></i>
                                            Update
                                            </p>
                                            <h2>7500</h2>
                                            <label class="badge badge-outline-success badge-pill">57% increase</label>
                                        </div>-->
                                        <div class="statistics-item">
                                            <p>
                                            <i class="icon-sm fas fa-chart-line mr-2"></i>
                                            Ventas
                                            </p>
                                            <h2>260</h2>
                                            <label class="badge badge-outline-success badge-pill">25 hoy</label>
                                        </div>
                                        <div class="statistics-item">
                                            <p>
                                            <i class="icon-sm fas fa-circle-notch mr-2"></i>
                                            Compras
                                            </p>
                                            <h2>200</h2>
                                            <label class="badge badge-outline-danger badge-pill">5 hoy</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <!--<div class="row">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                            <div class="card-body">
                                    <h4 class="card-title">
                                        <i class="fas fa-gift"></i>
                                        Compras del Mes
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
                                        Ventas del Mes
                                    </h4>
                                    <h2 class="mb-5">56000 <span class="text-muted h4 font-weight-normal">Ventas</span></h2>
                                    <canvas id="sales-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>-->

                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <i class="fas fa-chart-line"></i>
                                        Comportamiento de la Demanda vs Pronóstico BARRAS
                                    </h4>
                                    <!--<h2 class="mb-5">56000 <span class="text-muted h4 font-weight-normal">Ventas</span></h2>-->
                                    <canvas id="pronostico"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <i class="fas fa-chart-line"></i>
                                        Comportamiento de la Demanda vs Pronóstico LINEAS
                                    </h4>
                                    <!--<h2 class="mb-5">56000 <span class="text-muted h4 font-weight-normal">Ventas</span></h2>-->
                                    <canvas id="pronosticolineas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">

                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title">
                                            <i class="fas fa-table"></i>
                                            Data historica
                                        </h4>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="order-listing" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($brands as $brand)
                                                    <tr>
                                                        <th scope="row">{{$brand->id}}</th>
                                                        <td>
                                                            <a href="{{route('brands.show',$brand)}}">{{$brand->name}}</a>
                                                        </td>

                                                        <td>{{$brand->description}}</td>

                                                        <td style="width: 50px;">
                                                            {!! Form::open(['route'=>['brands.destroy',$brand], 'method'=>'DELETE']) !!}
                                                                <a class="jsgrid-button jsgrid-edit-button" href="{{route('brands.edit', $brand)}}" title="Editar">
                                                                    <i class="far fa-edit"></i>
                                                                </a>

                                                                <button class="jsgrid-button jsgrid-delete-button unstyled-button" type="submit" title="Eliminar">
                                                                    <i class="far fa-trash-alt"></i>
                                                                </button>

                                                                <a class="jsgrid-button jsgrid-edit-button" href="{{route('brands.show',$brand)}}" title="Ver Productos Relacionados">
                                                                    <i class="far fa-eye"></i>
                                                                </a>
                                                            {!! Form::close() !!}
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
                    <!--<div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                            <h4 class="card-title">
                                <i class="fas fa-table"></i>
                                Ultimas 10 Ventas
                            </h4>
                            <div class="table-responsive">
                                <table class="table">
                                <thead>
                                    <tr>
                                    <th>Customer</th>
                                    <th>Item code</th>
                                    <th>Orders</th>
                                    <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td class="font-weight-bold">
                                        Clifford Wilson
                                    </td>
                                    <td class="text-muted">
                                        PT613
                                    </td>
                                    <td>
                                        350
                                    </td>
                                    <td>
                                        <label class="badge badge-success badge-pill">Dispatched</label>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td class="font-weight-bold">
                                        Mary Payne
                                    </td>
                                    <td class="text-muted">
                                        ST456
                                    </td>
                                    <td>
                                        520
                                    </td>
                                    <td>
                                        <label class="badge badge-warning badge-pill">Processing</label>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td class="font-weight-bold">
                                        Adelaide Blake
                                    </td>
                                    <td class="text-muted">
                                        CS789
                                    </td>
                                    <td>
                                        830
                                    </td>
                                    <td>
                                        <label class="badge badge-danger badge-pill">Failed</label>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td class="font-weight-bold">
                                        Adeline King
                                    </td>
                                    <td class="text-muted">
                                        LP908
                                    </td>
                                    <td>
                                        579
                                    </td>
                                    <td>
                                        <label class="badge badge-primary badge-pill">Delivered</label>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td class="font-weight-bold">
                                        Bertie Robbins
                                    </td>
                                    <td class="text-muted">
                                        HF675
                                    </td>
                                    <td>
                                        790
                                    </td>
                                    <td>
                                        <label class="badge badge-info badge-pill">On Hold</label>
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>-->
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-table"></i>
                                    Pronostico vs data historica
                                </h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Cantidad</th>
                                                <th>Pronóstico</th>
                                                <th>Mes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pronosticos as $pronostico)
                                                <tr>
                                                    <td>{{$pronostico['cant_prod_vendidos']}}</td>
                                                    <td>{{$pronostico['pronostico']}}</td>
                                                    <td>{{$pronostico['mes']}}</td>
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
        </div>
    </div>
</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script>
    $(function(){

            var varPronostico=document.getElementById('pronostico').getContext('2d');
            var charPronostico = new Chart(varPronostico, {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($pronosticos as $pronostico)
                {
                    $fecha = $pronostico['mes'];

                    echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Demanda',
                        data: [<?php foreach ($pronosticos as $pron)
                        {echo ''. $pron['cant_prod_vendidos'].',';} ?>],
                        backgroundColor: '#2d2d86',
                        borderColor: '#2d2d86',
                        borderWidth: 5,
                    },{
                        label: 'Pronostico',
                        data: [<?php foreach ($pronosticos as $pron1)
                        {echo ''. $pron1['pronostico'].',';} ?>],
                        backgroundColor: '#994d00',
                        borderColor: '#994d00',
                        borderWidth: 5,
                    }]
                },
                options: {
                    /*
                    title:{
                        display:true,
                        text: 'Comportamiento de la Demanda vs Pronóstico',
                        fontSize: 30,
                        padding:30,
                        fontColor: '#12619c'
                    },*/
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 20,
                            beginAtZero:true
                        }
                      }]
                    },
                    legend: {
                      position: 'bottom',
                      display: true,
                      boxWidth: 25,
                      fontFamily: 'system-ui',
                      fontColor:'black'
                    },
                    elements: {
                      point: {
                        radius: 5
                      }
                    }
                }
            });


            var varPronosticolin=document.getElementById('pronosticolineas').getContext('2d');
            var charPronosticolin = new Chart(varPronosticolin, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticos as $pronostico)
                {
                    $fecha = $pronostico['mes'];
                    //$fecha = new Intl.DateTimeFormat('es-BO',{month:'long',year:'numeric'}).format(new Date($pronostico['mes']);
                    //$fecha = new Intl.DateTimeFormat('es-MX', { month: 'long', year: 'numeric' }).format(new Date($pronostico['mes'])));

                    echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Demanda',
                        data: [<?php foreach ($pronosticos as $pron)
                        {echo ''. $pron['cant_prod_vendidos'].',';} ?>],
                        backgroundColor: '#61529F',
                        borderColor: '#61529F',
                        borderWidth: 5,
                    },{
                        label: 'Pronostico',
                        data: [<?php foreach ($pronosticos as $pron1)
                        {echo ''. $pron1['pronostico'].',';} ?>],
                        backgroundColor: '#DD785D',
                        borderColor: '#DD785D',
                        borderWidth: 5,
                    }]
                },
                options: {
                    /*
                    title:{
                        display:true,
                        text: 'Comportamiento de la Demanda vs Pronóstico',
                        fontSize: 30,
                        padding:30,
                        fontColor: '#12619c'
                    },*/
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 20,
                            beginAtZero:true
                        }
                      }],
                      xAxes: [{
                        gridLines: {
                            stepSize: 12,
                            display:false
                        }
                      }]
                    },
                    legend: {
                      position: 'bottom',
                      display: true,
                      boxWidth: 50,
                      fontFamily: 'system-ui',
                      fontColor:'black'
                    },
                    tooltips:{
                      backgroundColor: '#1E2778',
                      titleFontSize:20,
                      xPadding:20,
                      yPadding:20,
                      bodyFontSize:15,
                      bodySpacing:10,
                      mode: 'x',
                    },
                    elements: {
                      line:{
                        borderWidth:1,
                        fill:false,
                      },
                      point: {
                        radius: 3,
                        borderWidth: 3,
                        backgroundColor: 'white',
                        hoverRadius: 6,
                        hoverborderWidth:2
                      }
                    }
                }
            });
    });
</script>
@endsection
