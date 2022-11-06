@extends('layouts.admin')
@section('title','Registrar Rol')
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
            Nuevo Rol
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Rol</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos del Rol</h4>
                    </div>

                    {!! Form::open(['route'=>'roles.store', 'method'=>'POST']) !!}

                        <div class="form-group">
                            <label for="name">Nombre:</label>
                            <input autofocus type="text" placeholder="Nombre" name="name" id="name" value="{{old('name')}}"
                            class="form-control @error('name') is-invalid @enderror">
                            <span class="invalid-feedback" role="alert">
                                <strong>{{$errors-> first('name')}}</strong>
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="slug">Slug (Url Amigable):</label>
                            <input autofocus type="text" placeholder="Slug" name="slug" id="slug" value="{{old('slug')}}"
                            class="form-control @error('slug') is-invalid @enderror">
                            <span class="invalid-feedback" role="alert">
                                <strong>{{$errors-> first('slug')}}</strong>
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción:</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"  placeholder="Descripción"name="description" id="description" value="{{old('description')}}" rows="3" >(Sin Descripción)</textarea>
                            <span class="invalid-feedback" role="alert">
                                <strong>{{$errors-> first('description')}}</strong>
                            </span>
                        </div>

                        <h3>Permisos Especiales</h3>
                        <div class="form-group">
                            <label> {!! Form::radio('special','all-access',['class' => 'form-group']) !!} Acceso Total </label>
                            <label> {!! Form::radio('special','no-access',['class' => 'form-group']) !!} Ningún Acceso </label>
                            <label> {!! Form::radio('special', '', true, ['class' => 'form-group'])!!} Personalizado </label>
                        </div>

                        <h3>Listado de Permisos</h3>
                        <div class="form-group">
                            <ul class="list-unstyled">
                                @foreach ($permissions as $permission)
                                    <li>
                                        <label>
                                            {!! Form::checkbox('permissions[]', $permission->id, null) !!}
                                            {{$permission->name}}
                                            <em>({{$permission->description}})</em>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Registrar</button>

                        <a href="{{route('roles.index')}}" class="btn btn-light">
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
