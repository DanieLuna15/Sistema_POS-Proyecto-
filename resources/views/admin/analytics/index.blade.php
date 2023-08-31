@extends('layouts.admin')
@section('title','Analítica')
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
            Analítica
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item active" aria-current="page">Analítica</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <ul class="nav nav-tabs" role="tablist" >
                    <li class="nav-item">
                        <a class="nav-link active" id="rango-tab" data-toggle="tab" href="#rango-1" role="tab" aria-controls="rango-1" aria-selected="true">Pronóstico General</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="diaria-tab" data-toggle="tab" href="#diaria-1" role="tab" aria-controls="diaria-1" aria-selected="false">Pronóstico Por Categoría</a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link" id="mensual-tab" data-toggle="tab" href="#mensual-1" role="tab" aria-controls="mensual-1" aria-selected="false">Mas</a>
                    </li>-->
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="rango-1" role="tabpanel" aria-labelledby="rango-tab">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <!--<div class="card-body">-->
                                        <ul class="nav nav-pills nav-pills-primary justify-content-between" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Histórico-Pronóstico hasta 2022</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Histórico-Pronóstico hasta 2023</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Solo Pronóstico hasta 2023</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                                <div class="col-lg-12">
                                                    <!--<div class="card">-->
                                                        <h4 class="card-title">
                                                            <i class="fas fa-chart-line"></i>
                                                            Comportamiento de la Demanda vs Pronóstico Hasta 2022
                                                        </h4>
                                                        <canvas id="pronosticolineas"></canvas>
                                                    <!--</div>-->
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                                <div class="col-lg-12">
                                                    <!--<div class="card">-->
                                                        <h4 class="card-title">
                                                            <i class="fas fa-chart-line"></i>
                                                            Comportamiento de la Demanda vs Pronóstico Hasta 2023
                                                        </h4>
                                                        <canvas id="pronosticolineasfuturo"></canvas>
                                                    <!--</div>-->
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                                                <div class="col-lg-12">
                                                    <!--<div class="card">-->
                                                        <h4 class="card-title">
                                                            <i class="fas fa-chart-line"></i>
                                                            Pronóstico de demanda Hasta 2023
                                                        </h4>
                                                        <canvas id="pronosticolineassolo2023"></canvas>
                                                    <!--</div>-->
                                                </div>
                                            </div>
                                        </div>
                                     <!--</div>-->
                                </div>
                            </div>
                        </div>

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
                            <div class="col-md-8 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">
                                                <i class="fas fa-table"></i>
                                                Data historica Mensual por Categorías
                                            </h4>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="order-listing" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Id</th>
                                                        <th>Fecha-Mes</th>
                                                        <th>Total /Bs.</th>
                                                        <th>Categoría-Top 1</th>
                                                        <th>Cantidad-Vendida/Unidades</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($historydatas as $historydata)
                                                        <tr>
                                                            <th scope="row">{{$historydata->id}}</th>
                                                            <td>{{ Carbon\Carbon::parse($historydata->month_date)->formatLocalized('%B - %Y')}}</td>
                                                            <td>{{$historydata->total_bs}}</td>
                                                            <td>{{$historydata->category->name}}</td>
                                                            <td align="center">
                                                                <a class="jsgrid-button btn btn-info btn-sm btn-rounded">
                                                                    <strong>{{$historydata->quantity}}</strong>
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
                                            <i class="fas fa-chart-pie"></i>
                                            Resumen Cantidad Vendida por Categoría
                                        </h4>
                                        <div class="flex-grow-2d-flex flex-column justify-content-between">
                                            <canvas id="sales-status-chart" class="mt-5"></canvas>
                                            <div class="pt-4">
                                                <div id="sales-status-chart-legend" class="sales-status-chart-legend"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="diaria-1" role="tabpanel" aria-labelledby="diaria-tab">
                        <div class="row">
                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Pronóstico por Categorías</h4>
                                        <p class="card-description">En esta sección podra visualizar el pronostico generado para todas las categorías en base a data histórica</p>
                                        <div class="mt-4">
                                            <div class="accordion accordion-solid-header" id="accordion-4" role="tablist">
                                                <div class="card">
                                                    <div class="card-header" role="tab" id="heading-10">
                                                        <h6 class="mb-0">
                                                            <a data-toggle="collapse" href="#collapse-10" aria-expanded="true" aria-controls="collapse-10">
                                                                <center>Laptops | Computadoras y Componentes</center>
                                                            </a>
                                                        </h6>
                                                    </div>
                                                    <div id="collapse-10" class="collapse show" role="tabpanel" aria-labelledby="heading-10" data-parent="#accordion-4">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <h4 class="card-title">
                                                                        <i class="fas fa-chart-line"></i>
                                                                        Demanda 2022-2023 Cat: Laptops.
                                                                    </h4>
                                                                    <canvas id="pronosticolineascat9"></canvas>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <h4 class="card-title">
                                                                        <i class="fas fa-chart-line"></i>
                                                                        Demanda 2022-2023 Cat: Computadoras y Componentes.
                                                                    </h4>
                                                                    <canvas id="pronosticolineascat8"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header" role="tab" id="heading-11">
                                                        <h6 class="mb-0">
                                                            <a class="collapsed" data-toggle="collapse" href="#collapse-11" aria-expanded="false" aria-controls="collapse-11">
                                                                <center>Fotografía | Consolas de Videojuegos</center>
                                                            </a>
                                                        </h6>
                                                    </div>
                                                    <div id="collapse-11" class="collapse" role="tabpanel" aria-labelledby="heading-11" data-parent="#accordion-4">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <!--<div class="card">-->
                                                                        <h4 class="card-title">
                                                                            <i class="fas fa-chart-line"></i>
                                                                            Demanda 2022-2023 Cat: Fotografía.
                                                                        </h4>
                                                                        <canvas id="pronosticolineascat1"></canvas>
                                                                    <!--</div>-->
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <!--<div class="card">-->
                                                                        <h4 class="card-title">
                                                                            <i class="fas fa-chart-line"></i>
                                                                            Demanda 2022-2023 Cat: Consolas de Videojuegos.
                                                                        </h4>
                                                                        <canvas id="pronosticolineascat5"></canvas>
                                                                    <!--</div>-->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header" role="tab" id="heading-12">
                                                        <h6 class="mb-0">
                                                            <a class="collapsed" data-toggle="collapse" href="#collapse-12" aria-expanded="false" aria-controls="collapse-12">
                                                                <center>Seguridad Y Vigilancia | Audio</center>
                                                            </a>
                                                        </h6>
                                                    </div>
                                                    <div id="collapse-12" class="collapse" role="tabpanel" aria-labelledby="heading-12" data-parent="#accordion-4">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <h4 class="card-title">
                                                                        <i class="fas fa-chart-line"></i>
                                                                        Demanda 2022-2023 Cat: Seguridad Y Vigilancia.
                                                                    </h4>
                                                                    <canvas id="pronosticolineascat2"></canvas>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <h4 class="card-title">
                                                                        <i class="fas fa-chart-line"></i>
                                                                        Demanda 2022-2023 Cat: Audio.
                                                                    </h4>
                                                                    <canvas id="pronosticolineascat3"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header" role="tab" id="heading-13">
                                                        <h6 class="mb-0">
                                                            <a class="collapsed" data-toggle="collapse" href="#collapse-13" aria-expanded="false" aria-controls="collapse-13">
                                                                <center>Dispositivos Audivisuales | Proyectores de Video</center>
                                                            </a>
                                                        </h6>
                                                    </div>
                                                    <div id="collapse-13" class="collapse" role="tabpanel" aria-labelledby="heading-13" data-parent="#accordion-4">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <h4 class="card-title">
                                                                        <i class="fas fa-chart-line"></i>
                                                                        Demanda 2022-2023 Cat: Dispositivos Audivisuales.
                                                                    </h4>
                                                                    <canvas id="pronosticolineascat4"></canvas>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <h4 class="card-title">
                                                                        <i class="fas fa-chart-line"></i>
                                                                        Demanda 2022-2023 Cat: Proyectores de Video.
                                                                    </h4>
                                                                    <canvas id="pronosticolineascat7"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header" role="tab" id="heading-14">
                                                        <h6 class="mb-0">
                                                            <a class="collapsed" data-toggle="collapse" href="#collapse-14" aria-expanded="false" aria-controls="collapse-14">
                                                                <center>Almacenamiento | Redes</center>
                                                            </a>
                                                        </h6>
                                                    </div>
                                                    <div id="collapse-14" class="collapse" role="tabpanel" aria-labelledby="heading-14" data-parent="#accordion-4">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <h4 class="card-title">
                                                                        <i class="fas fa-chart-line"></i>
                                                                        Demanda 2022-2023 Cat: Almacenamiento.
                                                                    </h4>
                                                                    <canvas id="pronosticolineascat6"></canvas>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <h4 class="card-title">
                                                                        <i class="fas fa-chart-line"></i>
                                                                        Demanda 2022-2023 Cat: Redes.
                                                                    </h4>
                                                                    <canvas id="pronosticolineascat10"></canvas>
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
                        </div>
                    </div>
                    <!--<div class="tab-pane fade" id="mensual-1" role="tabpanel" aria-labelledby="mensual-tab">
                        mas
                    </div>-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
{!! Html::script('melody/js/chart.js') !!}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

<script>
    $(function(){
        if ($("#sales-status-chart").length) {
        var pieChartCanvas = $("#sales-status-chart").get(0).getContext("2d");
        var pieChart = new Chart(pieChartCanvas, {
            type: 'pie',


            data: {
            datasets: [{
                data: [<?php foreach ($total_datahis as $total)
                  {echo ''. $total->quantity.',';} ?>],

                backgroundColor: [
                '#6C3483',
                '#C0392B',
                '#56C5D8',
                '#3C7A93',
                '#392765',
                '#04b321',
                '#ff5431',
                '#ff9900',
                '#392414',
                '#A3CF43'
                ],
                borderColor: [
                '#6C3483',
                '#C0392B',
                '#56C5D8',
                '#3C7A93',
                '#392765',
                '#04b321',
                '#ff5431',
                '#ff9900',
                '#392414',
                '#A3CF43'
                ],
            }],


            labels: [
                <?php foreach($total_datahis as $totalnames):?>
                    "<?php echo $totalnames->namecategory?>",
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
                text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">'+ chart.data.datasets[0].data[i] + ' Unidades</label>');
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
<script>
    $(function(){
            var varPronostico=document.getElementById('pronostico').getContext('2d');
            var charPronostico = new Chart(varPronostico, {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($pronosticosfuturo as $pronostico)
                {
                    $fecha = $pronostico['mes'];

                    echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Demanda',
                        data: [<?php foreach ($pronosticosfuturo as $pron)
                        {echo ''. $pron['cant_prod_vendidos'].',';} ?>],
                        backgroundColor: '#2d2d86',
                        borderColor: '#2d2d86',
                        borderWidth: 5,
                    },{
                        label: 'Pronostico',
                        data: [<?php foreach ($pronosticosfuturo as $pron1)
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

                    echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Demanda',
                        data: [<?php foreach ($pronosticos as $pron)
                        {echo ''. $pron['cant_prod_vendidos'].',';} ?>],
                        backgroundColor: 'rgba(101,105,166,0.55)',
                        borderColor: '#61529F',
                        borderWidth: 3,
                    },{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticos as $pron1)
                        {echo ''. $pron1['pronostico'].',';} ?>],
                        backgroundColor: 'rgba(195,110,37,0.55)',
                        borderColor: '#DD785D',
                        borderWidth: 3,
                    },{
                        label: 'Pronostico',
                        data: [NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,'150','102'],
                        backgroundColor: 'rgba(195,110,37,0.55)',
                        borderColor: '#DD785D',
                        borderWidth: 3,
                    }]
                },
                options: {
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
                        fontColor:'black',
                        labels:{
                            filter: function(item, chart) {
                                return !item.text.includes('Pronostico');
                            }
                        }
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolin=document.getElementById('pronosticolineasfuturo').getContext('2d');
            var charPronosticolin = new Chart(varPronosticolin, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticosfuturo as $pronostico)
                {
                    $fecha = $pronostico['mes'];

                    echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Demanda',
                        data: [<?php foreach ($pronosticosfuturo as $pron)
                        {echo ''. $pron['cant_prod_vendidos'].',';} ?>],
                        backgroundColor: 'rgba(101,105,166,0.55)',
                        borderColor: '#61529F',
                        borderWidth: 3,
                    },{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticosfuturo as $pron1)
                        {echo ''. $pron1['pronostico'].',';} ?>],
                        backgroundColor: 'rgba(195,110,37,0.55)',
                        borderColor: '#DD785D',
                        borderWidth: 3,
                    },{
                        label: 'Pronóstico',
                        data: [NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,'150','102'],
                        backgroundColor: 'rgba(195,110,37,0.55)',
                        borderColor: '#DD785D',
                        borderWidth: 3,
                    }]
                },
                options: {
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
                        fontColor:'black',
                        labels:{
                            filter: function(item, chart) {
                                return !item.text.includes('Pronostico');
                            }
                        }
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolin=document.getElementById('pronosticolineassolo2023').getContext('2d');
            var charPronosticolin = new Chart(varPronosticolin, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($solopronosticosfuturo as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Estimación',
                        data: [<?php foreach ($solopronosticosfuturo as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'rgba(195,110,37,0.55)',
                        borderColor: '#DD785D',
                        borderWidth: 3,
                    },{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidos as $prod)
                        {echo ''. $prod->cantidad.',';}?>6,5],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    },{
                        label: 'Cantidad Vendida 2023',
                        data: [NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,NaN,<?php foreach ($productosvendidosact as $prodact)
                        {echo ''. $prodact->cantidad.',';} ?>],
                        backgroundColor: 'RGBA(255,153,0,0.50)',
                        borderColor: '#ff9900',
                        borderWidth: 3,
                    }]
                },
                options: {
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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


            //para pronosticos de cada categoria
            var varPronosticolincat1=document.getElementById('pronosticolineascat1').getContext('2d');
            var charPronosticolincat1 = new Chart(varPronosticolincat1, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat1 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat1 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(108,52,131,0.50)',
                        borderColor: '#6C3483',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat1 as $prodcat1)
                        {echo ''. $prodcat1->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat2=document.getElementById('pronosticolineascat2').getContext('2d');
            var charPronosticolincat2 = new Chart(varPronosticolincat2, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat2 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat2 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(192,57,43,0.50)',
                        borderColor: '#C0392B',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat2 as $prodcat2)
                        {echo ''. $prodcat2->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat3=document.getElementById('pronosticolineascat3').getContext('2d');
            var charPronosticolincat3 = new Chart(varPronosticolincat3, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat3 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat3 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(86,197,216,0.50)',
                        borderColor: '#56C5D8',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat3 as $prodcat3)
                        {echo ''. $prodcat3->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat4=document.getElementById('pronosticolineascat4').getContext('2d');
            var charPronosticolincat4 = new Chart(varPronosticolincat4, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat4 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat4 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(60,122,147,0.50)',
                        borderColor: '#3C7A93',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat4 as $prodcat4)
                        {echo ''. $prodcat4->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat5=document.getElementById('pronosticolineascat5').getContext('2d');
            var charPronosticolincat5 = new Chart(varPronosticolincat5, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat5 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat5 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(57,39,101,0.50)',
                        borderColor: '#392765',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat5 as $prodcat5)
                        {echo ''. $prodcat5->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat6=document.getElementById('pronosticolineascat6').getContext('2d');
            var charPronosticolincat6 = new Chart(varPronosticolincat6, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat6 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat6 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(4,179,33,0.50)',
                        borderColor: '#04b321',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat6 as $prodcat6)
                        {echo ''. $prodcat6->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat7=document.getElementById('pronosticolineascat7').getContext('2d');
            var charPronosticolincat7 = new Chart(varPronosticolincat7, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat7 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat7 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(255,84,49,0.50)',
                        borderColor: '#ff5431',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat7 as $prodcat7)
                        {echo ''. $prodcat7->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat8=document.getElementById('pronosticolineascat8').getContext('2d');
            var charPronosticolincat8 = new Chart(varPronosticolincat8, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat8 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat8 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(255,153,0,0.50)',
                        borderColor: '#ff9900',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat8 as $prodcat8)
                        {echo ''. $prodcat8->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat9=document.getElementById('pronosticolineascat9').getContext('2d');
            var charPronosticolincat9 = new Chart(varPronosticolincat9, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat9 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat9 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(57,36,20,0.50)',
                        borderColor: '#392414',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat9 as $prodcat9)
                        {echo ''. $prodcat9->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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

            var varPronosticolincat10=document.getElementById('pronosticolineascat10').getContext('2d');
            var charPronosticolincat10 = new Chart(varPronosticolincat10, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($pronosticocat10 as $pronostico)
                    { $fecha = $pronostico['mes']; echo '"'. $fecha.'",';} ?>],
                    datasets: [{
                        label: 'Pronóstico',
                        data: [<?php foreach ($pronosticocat10 as $pron2)
                        {echo ''. $pron2['pronostico'].',';} ?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }/*,{
                        label: 'Cantidad Vendida 2022',
                        data: [<?php foreach ($productosvendidoscat10 as $prodcat10)
                        {echo ''. $prodcat10->cantidad.',';}?>],
                        backgroundColor: 'RGBA(163,207,67,0.50)',
                        borderColor: '#A3CF43',
                        borderWidth: 3,
                    }*/]
                },
                options: {
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 5,
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
                        boxWidth: 20,
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
                        tension : 0.4,
                        borderWidth:1,
                        fill:true,
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
