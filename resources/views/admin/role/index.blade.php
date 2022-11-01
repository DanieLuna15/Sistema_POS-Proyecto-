@extends('layouts.admin')
@section('title','Gestión de Roles del Sistema')
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
             Gestión de Roles del Sistema
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Roles del Sistema</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Roles del Sistema:</h4>
                        <div class="btn-group">
                            <a href="{{route('roles.create')}}" type="button" class="btn btn-info ">
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
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <th scope="row">{{$role->id}}</th>
                                        <td>
                                            <a href="{{route('roles.show',$role)}}">{{$role->name}}</a>
                                        </td>

                                        <td>{{$role->description}}</td>

                                        <td style="width: 50px;">
                                            {!! Form::open(['route'=>['roles.destroy',$role], 'method'=>'DELETE']) !!}
                                                <a class="jsgrid-button jsgrid-edit-button" href="{{route('roles.edit', $role)}}" title="Editar">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <!--
                                                <a class="jsgrid-button jsgrid-delete-button unstyled-button" href="{{route('roles.edit', $role)}}" title="Editar">
                                                    <i class="far fa-edit"></i>
                                                </a>

                                                <button class="jsgrid-button jsgrid-delete-button unstyled-button" type="submit" title="Eliminar">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>-->

                                                <a class="jsgrid-button jsgrid-edit-button" href="{{route('roles.show',$role)}}" title="Ver Productos Relacionados">
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
</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
@endsection
