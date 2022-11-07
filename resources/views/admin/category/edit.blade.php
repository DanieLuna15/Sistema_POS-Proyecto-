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
@endsection

@section('options')
@endsection

@section('preference')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Edición de Categoría:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
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
                        <h4 class="card-title">Datos de la Categoría:</h4>
                    </div>
                    {!! Form::model($category,['route'=>['categories.update',$category], 'method'=>'PUT']) !!}

                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" name="name" id="name" value="{{$category->name}}" class="form-control @error('name') is-invalid @enderror" placeholder="Nombre" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('name')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción:</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" placeholder="Descripcion" rows="3">{{$category->description}}</textarea>
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('description')}}</strong>
                        </span>
                    </div>

                     <button type="submit" class="btn btn-primary mr-2">Guardar Cambios</button>
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
