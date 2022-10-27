@extends('layouts.admin')
@section('title','Gestión de Clientes')
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
        Gestión de Clientes
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Clientes</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Clientes: </h4>
                        <div class="btn-group">
                            <a href="{{route('clients.create')}}" type="button" class="btn btn-info ">
                                <i class="fas fa-plus"></i> Nuevo
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Ci</th>
                                    <th>Telefono / Celular</th>
                                    <th>Correo Electrónico</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clients as $client): ?>
                                <tr>
                                    <th scope="row">{{$client->id}}</th>
                                        <td>
                                            <a href="{{route('clients.show',$client)}}">{{$client->name}}</a>
                                        </td>

                                        <td>{{$client->ci}}</td>
                                        <td>{{$client->phone}}</td>
                                        <td>{{$client->email}}</td>

                                        <td style="width: 50px;">
                                        {!! Form::open(['route'=>['clients.destroy',$client], 'method'=>'DELETE']) !!}
                                            <a class="jsgrid-button jsgrid-edit-button" href="{{route('clients.edit', $client)}}" title="Editar">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <!--
                                            <button class="jsgrid-button jsgrid-delete-button unstyled-button" type="submit" title="Eliminar">
                                                <i class="far fa-trash-alt"></i>
                                            </button>-->

                                            <a class="jsgrid-button jsgrid-edit-button" href="{{route('clients.show',$client)}}" title="Ver mas Información">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        {!! Form::close() !!}
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
@endsection
