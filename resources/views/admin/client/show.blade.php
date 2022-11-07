@extends('layouts.admin')
@section('title','información del cliente')
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
            Detalle del Cliente:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{route('clients.index')}}">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$client->name}}</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="border-bottom text-center pb-4">
                                <h3>{{$client->name}}</h3>
                                <div class="d-flex justify-content-between">
                                </div>
                            </div>
                            <div class="border-bottom py-4">
                                <div class="list-group">
                                    <a class="list-group-item list-group-item-action active" id="list-home-list"
                                        data-toggle="list" href="#list-home" role="tab" aria-controls="home">
                                        Datos del Cliente
                                    </a>
                                    <a class="list-group-item list-group-item-action" id="list-profile-list"
                                        data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">
                                        Historial de compras
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 pl-lg-5">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="list-home" user="tabpanel"
                                    aria-labelledby="list-home-list">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Información de cliente</h4>
                                        </div>
                                    </div>
                                    <div class="profile-feed">
                                        <div class="d-flex align-items-start profile-feed-item">
                                            <div class="form-group col-md-6">
                                                <strong><i class="fab fa-product-hunt mr-1"></i> Nombre:</strong>
                                                <p class="text-muted">
                                                    {{$client->name}}
                                                </p>
                                                <hr>
                                                <strong><i class="fas fa-address-card mr-1"></i> Numero CI:</strong>
                                                <p class="text-muted">
                                                    {{$client->ci}}
                                                </p>
                                                <hr>
                                                <strong><i class="fas fa-address-card mr-1"></i> NIT:</strong>
                                                <p class="text-muted">
                                                    {{$client->nit}}
                                                </p>
                                                <hr>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <strong>
                                                    <i class="fas fa-map-marked-alt mr-1"></i>Dirección:</strong>
                                                <p class="text-muted">
                                                    {{$client->address}}
                                                </p>
                                                <hr>
                                                <strong><i class=" fas fa-mobile mr-1"></i> Teléfono/Celular:</strong>
                                                <p class="text-muted">
                                                    {{$client->phone}}
                                                </p>
                                                <hr>
                                                <strong><i class=" fas fa-envelope mr-1"></i> Correo Electrónico:</strong>
                                                <p class="text-muted">
                                                    {{$client->email}}
                                                </p>
                                                <hr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="list-profile" user="tabpanel"
                                    aria-labelledby="list-profile-list">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Historial de compras</h4>
                                        </div>
                                    </div>
                                    <div class="profile-feed">
                                        <div class="d-flex align-items-start profile-feed-item">
                                            <div class="table-responsive">
                                                <table id="order-listing" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Total /Bs.</th>
                                                            <th>Fecha y Hora</th>
                                                            <th>Estado</th>
                                                            <th style="width:100px;">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($client->sales as $sale): ?>
                                                        <tr>
                                                            <th scope="row" style="width: 10%;">
                                                                <a href="{{route('sales.show', $sale)}}">{{$sale->id}}</a>
                                                            </th>
                                                            <td style="width: 10%;">{{$sale->total}}</td>
                                                            <td style="width: 15%;">{{ Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i:s') }}</td>
                                                            <td style="width: 7%;">
                                                                @if ($sale->status=='CONFIRMADO')
                                                                    <a class="jsgrid-button btn btn-success btn-sm btn-block" title="Deshabilitar" href="{{route('change.status.sales', $sale)}}">
                                                                        {{$sale->status}} <i class="fas fa-check"></i>
                                                                    </a>
                                                                @else
                                                                    <a class="jsgrid-button btn btn-danger btn-sm btn-block disabled" href="{{route('change.status.sales', $sale)}}">
                                                                        {{$sale->status}} <i class="fas fa-times"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td style="width: 25%;" align="center">
                                                                <a href="{{route('sales.pdf', $sale)}}" class="btn btn-outline-danger"
                                                                title="Generar PDF"><i class="far fa-file-pdf"></i></a>
                                                                <a href="" class="btn btn-outline-warning"
                                                                title="Imprimir boleta"><i class="fas fa-print"></i></a>
                                                                <a href="{{route('sales.show', $sale)}}" class="btn btn-outline-info"
                                                                title="Ver detalles"><i class="far fa-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                          <td colspan="3"><strong>Total de monto comprado: </strong></td>
                                                          <td colspan="2" align="left"><strong>Bs./{{$total_purchases}}</strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <a href="{{ URL::previous() }}" class="btn btn-primary float-right">Regresar</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/profile-demo.js') !!}
{!! Html::script('melody/js/data-table.js') !!}
@endsection
