@extends('layouts.admin')
@section('title','Gestión de Usuarios del Sistema')
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
             Gestión de Usuarios del Sistema
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item active" aria-current="page">Usuarios del Sistema</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Usuarios del Sistema:</h4>
                        <div class="btn-group">
                            <a href="{{route('users.create')}}" type="button" class="btn btn-info ">
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
                                    <th>Correo Electrónico</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <th scope="row">{{$user->id}}</th>

                                        <td>
                                            <a href="{{route('users.show',$user)}}">{{$user->name}}</a>
                                        </td>

                                        <td>{{$user->email}}</td>

                                        <td align="center">
                                            @foreach ($user->roles as $role)
                                                @if ($role->name=='')
                                                    <a class="jsgrid-button btn btn-warning btn-sm btn-rounded">Sin Asignar</a>
                                                @else
                                                    <a class="jsgrid-button btn btn-info btn-sm btn-rounded" href="{{route('roles.show',$role)}}">{{$role->name}}</a>
                                                @endif
                                            @endforeach
                                        </td>

                                        <td>
                                            @if ($user->status=='HABILITADO')
                                                <a class="jsgrid-button btn btn-success btn-sm btn-block" onclick="clic()">
                                                    {{$user->status}} <i class="fas fa-check"></i>
                                                </a>
                                            @else
                                                <a class="jsgrid-button btn btn-danger btn-sm btn-block" href="{{route('change.status.users', $user)}}">
                                                    {{$user->status}} <i class="fas fa-times"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td style="width: 20%;" align="center">
                                            {!! Form::open(['route'=>['users.destroy',$user], 'method'=>'DELETE']) !!}
                                                <a class="btn btn-outline-warning" href="{{route('users.edit', $user)}}" title="Editar">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-info" href="{{route('users.show',$user)}}" title="Ver Detalle">
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
{!! Html::script('js/my_functions.js') !!}
{!! Html::script('melody/js/alerts.js') !!}
{!! Html::script('melody/js/avgrund.js') !!}
@endsection
<script>
function clic() {
    swal({
        title: "¿Estas seguro?",
        text: "Si está deshabilitado ya no podrá acceder al sistema.",
        icon: "warning",
        buttons: true,
        buttons: ["Cancelar", "¡Si, estoy de acuerdo!"],
        dangerMode: true,
    }).then((result) => {
        if(result.isConfirmed){
            location.href='';
        }
    });
}
function clic1() {
    swal({
        title: "¿Estas seguro?",
        text: "Este usuario ya podrá acceder al sistema.",
        icon: "warning",
        buttons: true,
        buttons: ["Cancelar", "¡Si, estoy de acuerdo!"],
        dangerMode: true,
    }).then((result) => {
        if(result.isConfirmed){
            location.href='';
        }
    });
}
</script>
