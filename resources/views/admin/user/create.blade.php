@extends('layouts.admin')
@section('title','Registrar Usuario')
@section('styles')
<style type="text/css">
    .unstyled-button {
        border: none;
        padding: 0;
        background: none;
      }
</style>
@endsection

@section('options')
@endsection

@section('preference')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Nuevo Usuario
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Usuario</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos del Usuario</h4>
                    </div>

                    {!! Form::open(['route'=>'users.store', 'method'=>'POST']) !!}

                        <div class="form-group">
                            <label for="name">Nombre:</label>
                            <input autofocus type="text" placeholder="Nombre" name="name" id="name" value="{{old('name')}}"
                            class="form-control @error('name') is-invalid @enderror">
                            <span class="invalid-feedback" role="alert">
                                <strong>{{$errors-> first('name')}}</strong>
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo Electrónico: </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">@</span>
                                </div>
                                <input type="email"class="form-control @error('email') is-invalid @enderror" name="email"
                                id=email" aria-describedby="helpId" placeholder="alguien@example.com"  value="{{old('email')}}">
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$errors-> first('email')}}</strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña:</label>
                            <div class="input-group">
                                <input autofocus type="password" placeholder="Contraseña" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror">
                                <div class="input-group-append" onclick="Vista();">
                                    <button class="btn btn-sm btn-primary" title="Ver Contraseña"id="ver" type="button">
                                        <i class="far fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" title="Ocultar Contraseña" style="display:none;" id="ocultar" type="button">
                                        <i class="far fa-eye-slash"></i>
                                    </button>
                                </div>
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{$errors-> first('password')}}</strong>
                                </span>
                            </div>
                        </div>
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
                        <!--<div class="form-group">
                            <label for="password">Repetir Contraseña:</label>
                            <input autofocus type="text" placeholder="Nombre" name="password" id="password" value="{{old('password')}}" class="form-control @error('name') is-invalid @enderror" placeholder="Nombre">
                        </div>-->
                        <button type="submit" class="btn btn-primary mr-2">Registrar</button>
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
<script>
    function Vista(){
        let password=document.getElementById('password');
        let ver=document.getElementById('ver');
        let ocultar=document.getElementById('ocultar');
        if(password.type=='password'){
            password.type='text';
            ver.style.display='none';
            ocultar.style.display='block';
        } else {
            password.type='password';
            ver.style.display='block';
            ocultar.style.display='none';
        }
    }
</script>
@endsection
