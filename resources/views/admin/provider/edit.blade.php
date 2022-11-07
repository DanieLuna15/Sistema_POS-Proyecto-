@extends('layouts.admin')
@section('title','Editar Proveedor')
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
            Edición de Proveedor:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{route('providers.index')}}">Proveedores</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edición de Proveedor</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos del Proveedor:</h4>
                    </div>

                    {!! Form::model($provider,['route'=>['providers.update',$provider], 'method'=>'PUT']) !!}

                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        id="name" value="{{$provider->name}}" aria-describedby="helpId" placeholder="Ingrese el nombre del proveedor" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('name')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                        id="email" value="{{$provider->email}}" aria-describedby="emailHelpId" placeholder="ejemplo@gmail.com" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('email')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="nit">Numero NIT:</label>
                        <input type="number" class="form-control @error('nit') is-invalid @enderror" name="nit"
                        id="nit" value="{{$provider->nit}}" aria-describedby="helpId" placeholder="Ingrese el numero NIT" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('nit')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="address">Dirección/Pais:</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address"
                        id="address" value="{{$provider->address}}" aria-describedby="helpId" placeholder="Ingrese la dirección del Proveedor" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('address')}}</strong>
                        </span>
                        <small id="helpId" class="form-text text-muted">Éste campo es opcional</small>
                    </div>

                    <div class="form-group">
                        <label for="phone">Numero de contacto:</label>
                        <input type="number" class="form-control @error('phone') is-invalid @enderror" name="phone"
                        id="phone" value="{{$provider->phone}}" aria-describedby="helpId" placeholder="Ingrese el numero de Telefono/Celular" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('phone')}}</strong>
                        </span>
                    </div>

                     <button type="submit" class="btn btn-primary mr-2">Guardar Cambios</button>
                     <a href="{{route('providers.index')}}" class="btn btn-light">
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
