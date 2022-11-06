@extends('layouts.admin')
@section('title','Información sobre el usuario')
@section('styles')
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
            Detalle del Usuario
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('users.index')}}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$user->name}}</li>
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
                                <h3>{{$user->name}}</h3>
                                <div class="d-flex justify-content-between">
                                </div>
                            </div>
                            <div class="border-bottom py-4">
                                <div class="list-group">
                                    <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" user="tab" aria-controls="home">
                                        Datos del usuario
                                    </a>
                                    <a type="button" class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#list-profile" user="tab" aria-controls="profile">Historial de compras</a>
                                    <a type="button" class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list" href="#list-messages" user="tab" aria-controls="messages">Historial de ventas</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 pl-lg-5">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="list-home" user="tabpanel" aria-labelledby="list-home-list">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Información del usuario</h4>
                                        </div>
                                    </div>
                                    <div class="profile-feed">
                                        <div class="d-flex align-items-start profile-feed-item">
                                            <div class="form-group col-md-6">
                                                <strong><i class="fab fa-product-hunt mr-1"></i> Nombre</strong>
                                                <p class="text-muted">
                                                    {{$user->name}}
                                                </p>
                                                <hr>
                                                <strong><i class="fas fa-address-card mr-1"></i> Rol Asignado</strong>
                                                <p class="text-muted">
                                                    @foreach ($user->roles as $role)
                                                    <a href="{{route('roles.show',$role)}}">{{$role->name}}</a>
                                                    @endforeach
                                                </p>
                                                <hr>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <strong>
                                                    <i class=" fas fa-envelope mr-1"></i>
                                                    Correo electrónico</strong>
                                                <p class="text-muted">
                                                    {{$user->email}}
                                                </p>
                                                <hr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="list-profile" user="tabpanel" aria-labelledby="list-profile-list">
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
                                                            <th>Proveedor</th>
                                                            <th>Total /Bs.</th>
                                                            <th>Fecha y Hora</th>
                                                            <th>Estado</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($user->purchases as $purchase): ?>
                                                        <tr>
                                                            <th scope="row" style="width: 5%;">
                                                                <a href="{{route('purchases.show', $purchase)}}">{{$purchase->id}}</a>
                                                            </th>
                                                            <td style="width: 15%;">{{$purchase->provider->name}}</td>
                                                            <td style="width: 15%;">{{$purchase->total}}</td>
                                                            <td style="width: 15%;">{{ Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y H:i:s') }}</td>
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
                                                            <td style="width: 30%;" align="center">
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
                                                    <tfoot>
                                                        <tr>
                                                          <td colspan="2"><strong>Monto total en compras: </strong></td>
                                                          <td colspan="3" align="left"><strong>{{$total_amount_sold}} Bolivianos.</strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="list-messages" user="tabpanel" aria-labelledby="list-messages-list">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Historial de ventas</h4>
                                        </div>
                                    </div>
                                    <div class="profile-feed">
                                        <div class="d-flex align-items-start profile-feed-item">
                                            <div class="table-responsive">
                                                <table id="order-listing1" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Cliente</th>
                                                            <th>Total /Bs.</th>
                                                            <th>Fecha y Hora</th>
                                                            <th>Estado</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($user->sales as $sale): ?>
                                                        <tr>
                                                            <th scope="row" style="width: 5%;">
                                                                <a href="{{route('sales.show', $sale)}}">{{$sale->id}}</a>
                                                            </th>
                                                            <td style="width: 15%;">{{$sale->client->name}}</td>
                                                            <td style="width: 15%;">{{$sale->total}}</td>
                                                            <td style="width: 15%;">{{ Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i:s') }}</td>
                                                            <td style="width: 10%;">
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
                                                            <td style="width: 30%;" align="center">
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
                                                          <td colspan="2"><strong>Monto total en ventas: </strong></td>
                                                          <td colspan="3" align="left"><strong>{{$total_sales}} Bolivianos.</strong></td>
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
                    <a href="{{route('users.index')}}" class="btn btn-primary float-right">Regresar</a>
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
