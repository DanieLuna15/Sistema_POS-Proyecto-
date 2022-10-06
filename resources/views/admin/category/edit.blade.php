@extends('layouts.admin')
@section('title','Editar Categoría')
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
<!-- <li class="nav-item d-none d-lg-flex">
    <a class="nav-link" href="{{route('categories.create')}}">
        <span class="btn btn-primary">+ Crear nuevo</span>
    </a>
</li> -->

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
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">Categorías</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                    <center>
                        <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task-todo">Editar Categoría</button>
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
            Categorías
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Categorías</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edición de Categoría</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Edición de Categoría</h4>
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
                    {!! Form::model($category,['route'=>['categories.update',$category], 'method'=>'PUT']) !!}


                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" name="name" id="name" value="{{$category->name}}" class="form-control" placeholder="Nombre" >
                    </div>
                    <div class="form-group">
                        <label for="description">Descripción:</label>
                        <textarea class="form-control" name="description" id="description" placeholder="Descripcion" rows="3">{{$category->description}}</textarea>
                    </div>


                     <button type="submit" class="btn btn-primary mr-2">Actualizar</button>
                     <a href="{{route('categories.index')}}" class="btn btn-light">
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
