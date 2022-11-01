@extends('layouts.admin')
@section('title','Editar Usuario')
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
            Edición de Usuario:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('users.index')}}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edición de Usuario</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos de Usuario:</h4>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5>Por favor corrige los siguientes errores para poder continuar:</h5>
                            <ul>
                                @foreach ($errors->all() as $error )
                                    <li>{{$error}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {!! Form::model($user,['route'=>['users.update',$user], 'method'=>'PUT']) !!}
                        <div class="form-group">
                            <label for="name">Nombre:</label>
                            <input autofocus type="text" placeholder="Nombre" name="name" id="name" value="{{$user->name}}"
                            class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="email">Correo Electrónico: </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">@</span>
                                </div>
                                <input type="email"class="form-control" name="email"
                                id=email" aria-describedby="helpId" placeholder="alguien@example.com"  value="{{$user->email}}">
                            </div>
                        </div>

                        <!--<div class="form-group">
                            <label for="password">Contraseña:</label>
                            <input autofocus type="password" placeholder="Contraseña" name="password" id="password" value="{{$user->password}}"
                            class="form-control" placeholder="Nombre">
                        </div>-->

                        <h3>Listado de Roles</h3>
                        <div class="form-group">
                            <ul class="list-unstyled">
                                @foreach ($roles as $role)
                                    <li>
                                        <label>
                                            {!! Form::checkbox('roles[]', $role->id, null) !!}
                                            {{$role->name}}
                                            <em>({{$role->description}})</em>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Actualizar</button>
                        <a href="{{route('users.index')}}" class="btn btn-light">
                            Cancelar
                        </a>
                     {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
@endsection