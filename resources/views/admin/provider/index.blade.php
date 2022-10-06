@extends('layouts.admin')
@section('title','Gestión de Proveedores')
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
<li class="nav-item d-none d-lg-flex">
    <a class="nav-link" href="{{route('providers.create')}}">
        <span class="btn btn-primary">+ Crear nuevo</span>
    </a>
</li>
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
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">Gestión de Proveedores</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                    <a class="nav-link" href="{{route('providers.create')}}">
                        <span class="btn btn-primary">Registrar Proveedor</span>
                    </a>
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
        Gestión de Proveedores
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Proveedores</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Proveedores: </h4>

                        <div class="dropdown">
                          <button type="button" class="btn btn-dark dropdown-toggle" id="dropdownMenuIconButton7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuIconButton7">
                            <h6 class="dropdown-header">Acciones</h6>
                            <a class="dropdown-item" href="{{route('providers.create')}}">Agregar Nuevo +</a>
                            <a class="dropdown-item" href="#">Ver Historial</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Exportar a PDF</a>
                            <a class="dropdown-item" href="#">Exportar a Excel</a>
                          </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Correo Electronico</th>
                                    <th>Telefono/Celular</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($providers as $provider): ?>
                                <tr>
                                    <th scope="row">{{$provider->id}}</th>
                                        <td>
                                            <a href="{{route('providers.show',$provider)}}">{{$provider->name}}</a>
                                        </td>

                                        <td>{{$provider->email}}</td>
                                        <td>{{$provider->phone}}</td>

                                        <td style="width: 50px;">
                                        {!! Form::open(['route'=>['providers.destroy',$provider], 'method'=>'DELETE']) !!}
                                            <a class="jsgrid-button jsgrid-edit-button" href="{{route('providers.edit', $provider)}}" title="Editar">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <!--
                                            <button class="jsgrid-button jsgrid-delete-button unstyled-button" type="submit" title="Eliminar">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                            -->
                                            <a class="jsgrid-button jsgrid-edit-button" href="{{route('providers.show',$provider)}}" title="Ver mas Información">
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
