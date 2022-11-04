@extends('layouts.admin')
@section('title','Editar Producto')
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
            Edición de Producto:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('products.index')}}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edición de Producto</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos del Producto:</h4>
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

                    {!! Form::model($product,['route'=>['products.update',$product], 'method'=>'PUT','files' => true]) !!}
                    <!--'name', 'sell_price','category', 'brand','image',provider',-->

                    <div class="form-group">
                      <label for="name">Nombre Producto: </label>
                      <input type="text" class="form-control" name="name" value="{{  old('name', $product->name)}}"
                      id="name" aria-describedby="helpId" placeholder="Ingrese el nombre del producto">
                    </div>

                    <div class="form-group">
                      <label for="sell_price">Precio de Venta: </label>
                      <input type="number" class="form-control" name="sell_price" value="{{  old('sell_price', $product->sell_price)}}"
                      id="sell_price" aria-describedby="emailHelpId" placeholder="Precio de venta">
                    </div>

                    <div class="form-group">
                      <label for="category_id">Categoría:</label>
                      <select class="js-example-basic-single w-responsive form-control" name="category_id" id="category_id">
                        @foreach($categories as $category)
                        <option value="{{$category->id}}"
                            {{old('category_id', $product->category_id) == $category->id ? 'selected' : ''}}
                            >{{$category->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="brand_id">Marca:</label>
                      <select class="js-example-basic-single w-responsive form-control" name="brand_id" id="brand_id">
                        @foreach($brands as $brand)
                        <option value="{{$brand->id}}"
                            {{old('brand_id', $brand->brand_id) == $brand->id ? 'selected' : ''}}
                            >{{$brand->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="row">
                        <div class="col-md-9 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body col-lg-12">
                                    <h4 class="card-title d-flex">Imagen de Producto</h4>
                                    <input accept="image/*" type="file" name="picture" id="picture" class="dropify" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin">
                            <div class="col-lg-12">
                                <h4 class="card-title d-flex">Imagen Actual</h4>
                                <div class="card">
                                    <img src="{{asset('image/'.$product->image)}}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                      <label for="provider_id">Proveedor:</label>
                      <select class="js-example-basic-single w-responsive form-control" name="provider_id" id="provider_id">
                        @foreach($providers as $provider)
                        <option value="{{$provider->id}}"
                            {{old('provider_id', $provider->provider_id) == $provider->id ? 'selected' : ''}}
                            >{{$provider->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>

                     <button type="submit" class="btn btn-primary mr-2">Editar</button>
                     <a href="{{route('products.index')}}" class="btn btn-light">
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
{!! Html::script('melody/js/dropify.js') !!}
{!! Html::script('melody/js/select2.js') !!}
{!! Html::script('melody/js/light-gallery.js') !!}
@endsection
