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
                        Total Monto en Ventas del 2023
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
                        Total Ventas del 2023
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
                        Total Compras del 2023
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
                        <i class="fas fa-chart-line"></i>
                        Compras Vs Ventas del 2023
                    </h4>
                    <canvas id="barras"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Cantidad de Ventas: <a class="jsgrid-button btn btn-success btn-sm btn-rounded">
                                    <strong>{{$cantventasdia}}</strong>
                                </a>
                        realizadas en este mes.
                    </h4>
                    <canvas id="ventas_diarias"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
              <div class="card-body d-flex flex-column">
                <h4 class="card-title">
                  <i class="fas fa-chart-pie"></i>
                  Categorías mas Demandadas del 2023
                </h4>
                <p class="card-description">Este es un top 5 de las Categorías mas demandadas</p>
                <div class="flex-grow-1 d-flex flex-column justify-content-between">
                  <canvas id="sales-status-chart" class="mt-3"></canvas>
                  <div class="pt-4">
                    <div id="sales-status-chart-legend" class="sales-status-chart-legend"></div>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
              <div class="card-body d-flex flex-column">
                <h4 class="card-title">
                  <i class="fas fa-tachometer-alt"></i>
                  Marcas Mas Demandadas del 2023
                </h4>
                <p class="card-description">Este es un top 5 de las marcas mas demandadas</p>
                <div class="flex-grow-1 d-flex flex-column justify-content-between">
                  <canvas id="daily-sales-chart" class="mt-3 mb-3 mb-md-0"></canvas>
                  <div id="daily-sales-chart-legend" class="daily-sales-chart-legend pt-4 border-top"></div>
                </div>
              </div>
            </div>
          </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">
                            <i class="fas fa-table"></i>
                            Top 6 Productos con menor stock
                        </h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Stock Actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productosmenorstock as $productostockmin)
                                <tr>
                                    <td>{{$productostockmin->nameproduct}}</td>
                                    <td>
                                        @if ($productostockmin->stock==0)
                                            <a class="jsgrid-button btn btn-danger btn-sm btn-rounded">
                                                <strong>{{$productostockmin->stock}}</strong> Unidad(es)
                                            </a>
                                        @else
                                            <a class="jsgrid-button btn btn-warning btn-sm btn-rounded">
                                                <strong>{{$productostockmin->stock}}</strong> Unidad(es)
                                            </a>
                                        @endif
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
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="fas fa-table"></i>
                        Top 10 de Productos más vendidos del 2023
                    </h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th>Nombre</th>
                                    <th>Marca</th>
                                    <th>Stock Actual</th>
                                    <th>Cantidad vendida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productosmasvendidos as $productosmasvendido)
                                <tr>
                                    <td>
                                        <a class="jsgrid-button btn btn-dark btn-rounded">
                                            <strong>{{$productosmasvendido->id}}</strong>
                                        </a>
                                    </td>
                                    <td>{{$productosmasvendido->name}}</td>
                                    <td>{{$productosmasvendido->brand}}</td>
                                    <td>
                                        @if ($productosmasvendido->stock==0)
                                            <a class="jsgrid-button btn btn-danger btn-sm btn-rounded">
                                                <strong>{{$productosmasvendido->stock}}</strong> Unidad(es)
                                            </a>
                                        @else
                                            @if ($productosmasvendido->stock>15)
                                                <a class="jsgrid-button btn btn-success btn-sm btn-rounded">
                                                    <strong>{{$productosmasvendido->stock}}</strong> Unidad(es)
                                                </a>
                                            @else
                                                <a class="jsgrid-button btn btn-warning btn-sm btn-rounded">
                                                    <strong>{{$productosmasvendido->stock}}</strong> Unidad(es)
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a class="jsgrid-button btn btn-info btn-sm btn-rounded">
                                            <strong>{{$productosmasvendido->quantity}}</strong> Unidad(es)
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
    </div>
</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
{!! Html::script('melody/js/chart.js') !!}
<script>
    $(function(){
        if ($("#sales-status-chart").length) {
        var pieChartCanvas = $("#sales-status-chart").get(0).getContext("2d");
        var pieChart = new Chart(pieChartCanvas, {
            type: 'pie',


            data: {
            datasets: [{
                data: [<?php foreach ($categoríasmasvendidas as $category)
                  {echo ''. $category->quantity.',';}  ?>],

                backgroundColor: [
                '#392c70',
                '#04b76b',
                '#66ff33',
                '#ff5431',
                '#eee543'
                ],
                borderColor: [
                '#392c70',
                '#04b76b',
                '#66ff33',
                '#ff5431',
                '#eee543'
                ],
            }],

            labels: [
                <?php foreach($categoríasmasvendidas as $names):?>
                    "<?php echo $names->namecategory?>",
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
                text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">'+ chart.data.datasets[0].data[i] + ' Unidad(es)</label>');
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
        if ($("#daily-sales-chart").length) {
            var dailySalesChartData = {
                datasets: [{
                    data: [<?php foreach ($marcasmasvendidas as $brand)
                    {echo ''. $brand->quantity.',';}  ?>],
                backgroundColor: [
                    '#392c70',
                    '#04b76b',
                    '#66ff33',
                    '#ff5431',
                    '#eee543'
                ],
                borderWidth: 0
                }],

                labels: [
                <?php foreach($marcasmasvendidas as $namesb):?>
                    "<?php echo $namesb->namebrand?>",
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
                    text.push('<li><span class="legend-label" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"></span>');
                    if (chart.data.labels[i]) {
                    text.push(chart.data.labels[i]);
                    }
                    text.push('<label class="badge badge-light badge-pill legend-percentage ml-auto">'+ chart.data.datasets[0].data[i] + ' Unidad(es)</label>');
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
            var currentChartCanvas = document.getElementById('barras').getContext('2d');
            var currentChart = new Chart(currentChartCanvas, {
                type: 'bar',
                data: {
                labels: [
                    <?php foreach($comprasmes as $comprames):?>
                        "<?php echo $comprames->mes?>",
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Compras en BS.',
                    data: [<?php foreach ($comprasmes as $compra)
                                {echo ''. $compra->totalmes .',';} ?>],

                    backgroundColor: '#392c70'
                    },
                    {
                    label: 'Ventas en BS.',
                    data: [<?php foreach ($ventasmes as $venta)
                                {echo ''. $venta->totalmes.',';} ?>],
                    backgroundColor: '#d1cede'
                    }
                ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                      yAxes: [{
                        ticks: {
                            stepSize: 20000,
                            beginAtZero:true
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
                    elements: {
                      point: {
                        radius: 5
                      }
                    }
                }
            });

            var varVenta=document.getElementById('ventas_diarias').getContext('2d');
            var charVenta = new Chart(varVenta, {
                type: 'line',
                data: {
                    labels: [<?php foreach ($ventasdia as $ventadia){
                    $dia = $ventadia->date;
                    echo '"'. $dia.'",';
                    } ?>],
                    datasets: [{
                        label: 'Ventas',
                        data: [<?php foreach ($ventasdia as $reg)
                        {echo ''. $reg->count.',';} ?>],
                        backgroundColor: 'rgba(113, 97, 185, 0.8)',
                        borderColor: '#392c70',
                        borderWidth: 2
                        }]
                },
                options: {
                    scales: {
                      yAxes: [{
                        gridLines: {
                        drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,
                            beginAtZero:true
                        }
                      }],
                      xAxes: [{
                        display: true,
                        gridLines: {
                            drawBorder: false
                        }
                    }]
                    },
                    legend: {
                      display: false
                    },
                    elements: {
                      point: {
                        radius: 2
                      }
                    }
                }
            });
    });
</script>

<script>
    $(function(){
        if ($('#sales-chart').length) {
        var lineChartCanvas = $("#sales-chart").get(0).getContext("2d");
        var data = {
            labels: [<?php foreach ($ventasdia as $ventadia){
                    $dia = $ventadia->date;
                    echo '"'. $dia.'",';
                    } ?>],
            datasets: [
            {
                label: 'Ventas',
                data: [<?php foreach ($ventasdia as $reg)
                        {echo ''. $reg->count.',';} ?>],
                borderColor: [
                '#392c70'
                ],
                borderWidth: 3,
                fill: false
            },
            {
                label: 'Compras',
                data: [<?php foreach ($comprasdia as $reg)
                        {echo ''. $reg->count.',';} ?>],
                borderColor: [
                '#d1cede'
                ],
                borderWidth: 3,
                fill: false
            }
            ]
        };
        var options = {
            scales: {
            yAxes: [{
                gridLines: {
                drawBorder: false
                },
                ticks: {
                stepSize: 1,
                beginAtZero:true,
                fontColor: "#686868"
                }
            }],
            xAxes: [{
                display: true,
                gridLines: {
                drawBorder: false
                }
            }]
            },
            legend: {
            display: false
            },
            elements: {
            point: {
                radius: 3
            }
            },
            stepsize: 1
        };
        var lineChart = new Chart(lineChartCanvas, {
            type: 'line',
            data: data,
            options: options
        });
        }
    });
</script>

@endsection


