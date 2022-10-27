@extends('layouts.admin')
@section('title','Registrar Cliente')
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
            Nuevo Cliente
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('clients.index')}}">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('clients.index')}}">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Cliente</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Registro de Cliente</h4>
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
                        {!! Form::open(['route'=>'clients.store', 'method'=>'POST','files' => true]) !!}
                        <div class="form-group">
                        <label for="name">* Nombre: </label>
                        <input autofocus type="text"class="form-control" name="name"
                        id=name" aria-describedby="helpId" placeholder="Ingrese el nombre del Cliente"  value="{{old('name')}}">
                        </div>

                        <div class="form-group">
                        <label for="ci">* Cédula de Identidad: </label>
                        <input type="number"class="form-control" name="ci"
                        id=ci" aria-describedby="helpId" placeholder="Ingrese el número de Carnet"  value="{{old('ci')}}">
                        </div>

                        <div class="form-group">
                        <label for="nit">NIT: </label>
                        <input type="number"class="form-control" name="nit"
                        id=nit" aria-describedby="helpId" placeholder="Ingrese el número Nit"  value="{{old('nit')}}">
                        <small id="helpId" class="form-text text-muted">Éste campo es opcional</small>
                        </div>

                        <div class="form-group">
                        <label for="address">Dirección: </label>
                        <input type="text"class="form-control" name="address"
                        id=address" aria-describedby="helpId" placeholder="Ingrese la dirección"  value="{{old('address')}}">
                        <small id="helpId" class="form-text text-muted">Éste campo es opcional</small>
                        </div>


                        <div class="form-group">
                        <label for="phone">* Teléfono/Celular: </label>
                        <input type="number"class="form-control" name="phone"
                        id=phone" aria-describedby="helpId" placeholder="Ingrese el número de telefono/celular"  value="{{old('phone')}}">
                        </div>

                        <div class="form-group">

                            <label for="email">Correo Electrónico: </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">@</span>
                                </div>
                                <input type="email"class="form-control" name="email"
                        id=email" aria-describedby="helpId" placeholder="Ingrese el correo electrónico del cliente"  value="{{old('email')}}">
                            </div>
                            <small id="helpId" class="form-text text-muted">Éste campo es opcional</small>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Registrar</button>
                        <a href="{{route('clients.index')}}" class="btn btn-light">
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
