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
            Analítica
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Analítica</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">

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
                    <div class="col-lg-8 grid-margin stretch-card">
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
                                            <th>Total-BS</th>
                                            <th>Categoría-Top 1</th>
                                            <th>Cantidad-Vendida</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($historydatas as $historydata)
                                            <tr>
                                                <th scope="row">{{$historydata->id}}</th>
                                                <td>{{$historydata->month_date}}</td>
                                                <td>{{$historydata->total_bs}}</td>
                                                <td>{{$historydata->category->name}}</td>
                                                <td>{{$historydata->quantity}}</td>
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
                                Cantidad Vendida por Categoría
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
                '#392c70',
                '#04b76b',
                '#66ff33',
                '#eeeeee',
                '#392765',
                '#04b321',
                '#ff5431',
                '#ff9900',
                '#392414',
                '#eee543'
                ],
                borderColor: [
                '#392c70',
                '#04b76b',
                '#66ff33',
                '#eeeeee',
                '#392765',
                '#04b321',
                '#ff5431',
                '#ff9900',
                '#392414',
                '#eee543'
                ],
            }],

            // These labels appear in the legend and in the tooltips when hovering different arcs
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
