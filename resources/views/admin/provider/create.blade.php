@extends('layouts.admin')
@section('title','Registrar Proveedor')
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
<li class="nav-item nav-settings d-none d-lg-block">
    <a class="nav-link" href="#">
        <i class="fas fa-ellipsis-h"></i>
    </a>
</li>
@endsection
@section('preference')
    <div id="right-sidebar" class="settings-panel">
        <i class="settings-close fa fa-times"></i>
        <ul class="nav nav-tabs" id="setting-panel" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">Gestion de Proveedores</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                    <center>
                        <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task-todo">Registrar Proveedor</button>
                    </center>
                </li>
              </ul>
            </div>
          </div>
        </div>
    </div>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Proveedores
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('providers.index')}}">Proveedores</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Proveedor</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Registro de Proveedor</h4>
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

                    {!! Form::open(['route'=>'providers.store', 'method'=>'POST']) !!}

                    <!--'name', 'email','nit', 'address','phone',-->

                    <div class="form-group">
                      <label for="name">Nombre/Razón Social:</label>
                      <input autofocus type="text" class="form-control" name="name"
                      id="name" aria-describedby="helpId" placeholder="Ingrese el nombre del proveedor" >
                    </div>

                    <div class="form-group">
                      <label for="email">Correo Electrónico:</label>
                      <input type="email" class="form-control" name="email"
                      id="email" aria-describedby="emailHelpId" placeholder="ejemplo@gmail.com" >
                    </div>

                    <div class="form-group">
                      <label for="nit">Numero NIT:</label>
                      <input type="number" class="form-control" name="nit"
                      id="nit" aria-describedby="helpId" placeholder="Ingrese el numero NIT" >
                    </div>

                    <div class="form-group">
                      <label for="address">Dirección/Pais:</label>
                      <input type="text" class="form-control" name="address"
                      id="address" aria-describedby="helpId" placeholder="Ingrese la dirección del Proveedor" >
                    </div>

                    <div class="form-group">
                      <label for="phone">Numero de contacto:</label>
                      <input type="number" class="form-control" name="phone"
                      id="phone" aria-describedby="helpId" placeholder="Ingrese el numero de Telefono/Celular" >
                    </div>

                     <button type="submit" class="btn btn-primary mr-2">Registrar</button>
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
