@extends('layouts.admin')
@section('title','Gestión de Productos')
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
        Gestión de Productos
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Productos</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Productos: </h4>

                        <div class="btn-group">
                            <a href="{{route('products.create')}}" type="button" class="btn btn-info ">
                                <i class="fas fa-plus"></i> Nuevo
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Marca</th>
                                    <th>Stock/Unidades</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <th scope="row">{{$product->id}}</th>
                                        <td><img src="{{asset('image/'.$product->image)}}" class="img-lg rounded" alt="profile image" /></td>
                                        <td>
                                            <a href="{{route('products.show',$product)}}">{{$product->name}}</a>
                                        </td>
                                        <td>{{$product->category->name}}</td>

                                        <td>{{$product->brand->name}}</td>

                                        <td>
                                            @if ($product->stock==0)
                                                <a class="jsgrid-button btn btn-danger btn-sm btn-rounded">
                                                    <strong>{{$product->stock}}</strong>
                                                </a>
                                            @else
                                                @if ($product->stock>15)
                                                    <a class="jsgrid-button btn btn-success btn-sm btn-rounded">
                                                        <strong>{{$product->stock}}</strong>
                                                    </a>
                                                @else
                                                    <a class="jsgrid-button btn btn-warning btn-sm btn-rounded">
                                                        <strong>{{$product->stock}}</strong>
                                                    </a>
                                                @endif
                                            @endif
                                        </td>

                                        <td>
                                        @if ($product->status=='ACTIVO')
                                            <a class="jsgrid-button btn btn-success btn-sm btn-block" href="{{route('change.status.products', $product)}}">
                                                {{$product->status}} <i class="fas fa-check"></i>
                                            </a>
                                        @else
                                            <a class="jsgrid-button btn btn-danger btn-sm btn-block" href="{{route('change.status.products', $product)}}">
                                                {{$product->status}} <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                        </td>

                                        <td style="width: 20%;" align="center">
                                            {!! Form::open(['route'=>['products.destroy',$product], 'method'=>'DELETE']) !!}
                                                <a class="btn btn-outline-warning" href="{{route('products.edit', $product)}}" title="Editar">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-info" href="{{route('products.show',$product)}}" title="Ver Detalle">
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
