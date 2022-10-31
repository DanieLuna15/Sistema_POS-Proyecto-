@extends('layouts.admin')
@section('title','Registrar Marca')
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
            Nueva Marca:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('brands.index')}}">Marcas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Marca</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos de la Marca:</h4>
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

                    {!! Form::open(['route'=>'brands.store', 'method'=>'POST']) !!}
                        <div class="form-group">
                            <label for="name">Nombre:</label>
                            <input autofocus type="text" placeholder="Nombre" name="name" id="name" value="{{old('name')}}" class="form-control" placeholder="Nombre">
                        </div>
                        <div class="form-group">
                            <label for="description">Descripción:</label>
                            <textarea class="form-control"  placeholder="Descripción"name="description" id="description" value="(Sin Descripción)" rows="3" >(Sin Descripción)</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Registrar</button>
                        <a href="{{route('brands.index')}}" class="btn btn-light">
                            Cancelar
                        </a>
                     {!! Form::close() !!}
                </div>
                {{-- <div class="card-footer text-muted">
                        {{$brands->renderer()}}
                     </div>--}}
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
@endsection
