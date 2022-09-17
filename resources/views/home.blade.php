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

                    <div class="row grid-margin">
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

                    <div class="row">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                            <div class="card-body">
                                    <h4 class="card-title">
                                        <i class="fas fa-gift"></i>
                                        Pronóstico de demanda 2022
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
                                        Comportamiento de las Ventas
                                    </h4>
                                    <h2 class="mb-5">56000 <span class="text-muted h4 font-weight-normal">Ventas</span></h2>
                                    <canvas id="sales-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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
                    </div>






                        <div class="d-flex justify-content-between">


                            <!--<h4 class="card-title">Categorías:</h4>
                            <div class="dropdown">
                            <button type="button" class="btn btn-dark dropdown-toggle" id="dropdownMenuIconButton7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cog"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuIconButton7">
                                <h6 class="dropdown-header">Acciones</h6>
                                <a class="dropdown-item" href="{{route('categories.create')}}">Agregar Nuevo +</a>
                                <a class="dropdown-item" href="#">Ver Historial</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Exportar a PDF</a>
                                <a class="dropdown-item" href="#">Exportar a Excel</a>
                            </div>
                            </div>-->

                        </div>
                        {{--
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
                                    @foreach ($categories as $category)
                                        <tr>
                                            <th scope="row">{{$category->id}}</th>
                                            <td>
                                                <a href="{{route('categories.show',$category)}}">{{$category->name}}</a>
                                            </td>

                                            <td>{{$category->description}}</td>

                                            <td style="width: 50px;">
                                                {!! Form::open(['route'=>['categories.destroy',$category], 'method'=>'DELETE']) !!}
                                                    <a class="jsgrid-button jsgrid-edit-button" href="{{route('categories.edit', $category)}}" title="Editar">
                                                        <i class="far fa-edit"></i>
                                                    </a>

                                                    <button class="jsgrid-button jsgrid-delete-button unstyled-button" type="submit" title="Eliminar">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>

                                                    <a class="jsgrid-button jsgrid-edit-button" href="{{route('categories.show',$category)}}" title="Ver Productos Relacionados">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>--}}
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
