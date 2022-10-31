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
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
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
                    {!! Form::model($provider,['route'=>['providers.update',$provider], 'method'=>'PUT']) !!}

                    <!--'name', 'email','nit', 'address','phone',-->

                    <div class="form-group">
                      <label for="name">Nombre:</label>
                      <input type="text" class="form-control" name="name"
                      id="name" value="{{$provider->name}}" aria-describedby="helpId" placeholder="Ingrese el nombre del proveedor" >
                    </div>

                    <div class="form-group">
                      <label for="email">Correo Electrónico:</label>
                      <input type="email" class="form-control" name="email"
                      id="email" value="{{$provider->email}}" aria-describedby="emailHelpId" placeholder="ejemplo@gmail.com" >
                    </div>

                    <div class="form-group">
                      <label for="nit">Numero NIT:</label>
                      <input type="number" class="form-control" name="nit"
                      id="nit" value="{{$provider->nit}}" aria-describedby="helpId" placeholder="Ingrese el numero NIT" >
                    </div>

                    <div class="form-group">
                      <label for="address">Dirección/Pais:</label>
                      <input type="text" class="form-control" name="address"
                      id="address" value="{{$provider->address}}" aria-describedby="helpId" placeholder="Ingrese la dirección del Proveedor" >
                    </div>

                    <div class="form-group">
                      <label for="phone">Numero de contacto:</label>
                      <input type="number" class="form-control" name="phone"
                      id="phone" value="{{$provider->phone}}" aria-describedby="helpId" placeholder="Ingrese el numero de Telefono/Celular" >
                    </div>

                     <button type="submit" class="btn btn-primary mr-2">Actualizar</button>
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
