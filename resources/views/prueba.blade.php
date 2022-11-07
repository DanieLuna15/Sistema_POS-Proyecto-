@extends('layouts.admin')
@section('title','PRUEBA PLANTILLA')
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
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">PRUEBA</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                    <center>
                        <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task-todo">Registrar PRUEBA</button>
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
            PRUEBA
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item active" aria-current="page">PRUEBA</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">PRUEBA</h4>

                        <div class="btn-group">
                            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                              <a href="" class="dropdown-item">Agregar</a>
                             <a class="dropdown-item" href="">Exportar códigos de barras</a>
                              {{--  <button class="dropdown-item" type="button">Another action</button>
                              <button class="dropdown-item" type="button">Something else here</button>  --}}
                            </div>
                          </div>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--{{-- @foreach ($products as $product)
                                    <tr>
                                        <th scope="row">{{$product->id}}</th>
                                        <td>
                                            <a href="{{route('products.show',$product)}}">{{$product->name}}</a>
                                        </td>
                                        <td>{{$product->stock}}</td>
                                        @if ($product->status == 'ACTIVE')
                                        <td>
                                            <a class="jsgrid-button btn btn-success" href="{{route('change.status.products', $product)}}" title="Editar">
                                                Activo <i class="fas fa-check"></i>
                                            </a>
                                        </td>
                                        @else
                                        <td>
                                            <a class="jsgrid-button btn btn-danger" href="{{route('change.status.products', $product)}}" title="Editar">
                                                Desactivado <i class="fas fa-times"></i>
                                            </a>
                                        </td>
                                        @endif


                                         <td>{{$product->category->name}}</td>
                                        <td style="width: 50px;">
                                            {!! Form::open(['route'=>['products.destroy',$product], 'method'=>'DELETE']) !!}

                                            <a class="jsgrid-button jsgrid-edit-button" href="{{route('products.edit', $product)}}" title="Editar">
                                                <i class="far fa-edit"></i>
                                            </a>

                                            <button class="jsgrid-button jsgrid-delete-button unstyled-button" type="submit" title="Eliminar">
                                                <i class="far fa-trash-alt"></i>
                                            </button>

                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach --}}-->
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
