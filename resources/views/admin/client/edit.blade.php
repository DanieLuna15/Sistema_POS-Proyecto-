@extends('layouts.admin')
@section('title','Editar Cliente')
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
            Edición de Cliente:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('clients.index')}}">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edición de Cliente</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos del Cliente:</h4>
                    </div>

                    {!! Form::model($client,['route'=>['clients.update',$client], 'method'=>'PUT']) !!}

                    <div class="form-group">
                        <label for="name">* Nombre: </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$client->name}}"
                        id=name" aria-describedby="helpId" placeholder="Ingrese el nombre del Cliente">
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('nit')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="ci">* Cédula de Identidad: </label>
                        <input type="number" class="form-control @error('ci') is-invalid @enderror" name="ci" value="{{$client->ci}}"
                        id=ci" aria-describedby="helpId" placeholder="Ingrese el número de Carnet">
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('ci')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="nit">NIT: </label>
                        <input type="number" class="form-control @error('nit') is-invalid @enderror" name="nit" value="{{$client->nit}}"
                        id=nit" aria-describedby="helpId" placeholder="Ingrese el número Nit" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('nit')}}</strong>
                        </span>
                        <small id="helpId" class="form-text text-muted">Éste campo es opcional</small>
                    </div>

                    <div class="form-group">
                        <label for="address">Dirección: </label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{$client->address}}"
                        id=address" aria-describedby="helpId" placeholder="Ingrese la dirección" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('address')}}</strong>
                        </span>
                        <small id="helpId" class="form-text text-muted">Éste campo es opcional</small>
                    </div>

                    <div class="form-group">
                        <label for="phone">* Teléfono: </label>
                        <input type="number" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{$client->phone}}"
                        id=phone" aria-describedby="helpId" placeholder="Ingrese el número de telefono/celular" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('phone')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico: </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{$client->email}}"
                        id=email" aria-describedby="helpId" placeholder="Ingrese el número de telefono/celular" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('email')}}</strong>
                        </span>
                        <small id="helpId" class="form-text text-muted">Éste campo es opcional</small>
                    </div>

                     <button type="submit" class="btn btn-primary mr-2">Editar</button>
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
{!! Html::script('melody/js/dropify.js') !!}
@endsection
