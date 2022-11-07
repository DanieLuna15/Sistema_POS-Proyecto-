@extends('layouts.admin')
@section('title','Productos Relacionados')
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
            Productos que pertenecen a "{{$brand->name}}"
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{route('brands.index')}}">Marcas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Productos de: {{$brand->name}}</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Marca: '{{$brand->name}}'</h4>
                    </div>
                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Marca</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($brand->products as $product)
                                <tr>
                                    <th scope="row">{{$product->id}}</th>
                                    <td>
                                        <a href="{{route('products.show',$product)}}">{{$product->name}}</a>
                                    </td>

                                    <td>{{$product->stock}}</td>

                                    <td>{{$product->status}}</td>

                                    <td>{{$product->brand->name}}</td>

                                    <td style="width: 50px;" align="center">
                                        {!! Form::open(['route'=>['products.destroy',$product], 'method'=>'DELETE']) !!}

                                            <a class="jsgrid-button jsgrid-edit-button" href="{{route('brands.edit', $brand)}}" title="Editar">
                                                <i class="far fa-edit"></i>
                                            </a>

                                            <!--
                                            <button class="jsgrid-button jsgrid-delete-button unstyled-button" type="submit" title="Eliminar">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                            -->

                                            <a class="jsgrid-button jsgrid-edit-button" href="{{route('brands.show',$brand)}}" title="Ver Productos Relacionados">
                                                <i class="far fa-eye"></i>
                                            </a>

                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <a href="{{route('brands.index')}}" class="btn btn-primary float-right">Regresar</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('scripts')
{!! Html::script('melody/js/profile-demo.js') !!}
{!! Html::script('melody/js/data-table.js') !!}
@endsection
