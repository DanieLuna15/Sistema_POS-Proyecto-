@extends('layouts.admin')
@section('title','Registrar Producto')
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
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">PRODUCTOS</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                  <center>
                    <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task-todo">Registrar Producto</button>
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
            Productos
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('products.index')}}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Producto</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Registro de Producto</h4>
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

                    {!! Form::open(['route'=>'products.store', 'method'=>'POST','files' => true]) !!}

                    <!--'name', 'sell_price','category', 'brand','image',provider',-->


                    <div class="form-group">
                      <label for="name">Nombre Producto: </label>
                      <input autofocus type="text" class="form-control" name="name"
                      id="name" aria-describedby="helpId"  value="{{old('name')}}" placeholder="Nombre del producto">
                    </div>

                    <div class="form-group">
                      <label for="sell_price">Precio de Venta: </label>
                      <input type="number" class="form-control" name="sell_price"
                      id="sell_price" aria-describedby="emailHelpId" value="{{old('sell_price')}}" placeholder="Precio de venta" >
                    </div>

                    <div class="form-group">
                      <label for="category_id">Categoría:</label>
                      <select class="js-example-basic-single w-100" name="category_id" id="category_id">
                        @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="brand_id">Marca:</label>
                      <select class="js-example-basic-single w-100" name="brand_id" id="brand_id">
                        @foreach($brands as $brand)
                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                        @endforeach
                      </select>
                    </div>


                    <!--<div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" name="picture" id="picture" lang="es">
                        <label class="custom-file-label" for="imagen">Seleccionar Archivo</label>
                    </div> -->

                    <div class="card-body">
                      <h4 class="card-title d-flex">Imagen de Producto
                        <small class="ml-auto align-self-end">

                        </small>
                      </h4>
                      <input  type="file" name="picture" id="picture" class="dropify"  required/>
                    </div>


                    <div class="form-group">
                      <label for="provider_id">Proveedor:</label>
                      <select class="js-example-basic-single w-responsive form-control" name="provider_id" id="provider_id">
                        @foreach($providers as $provider)
                        <option value="{{$provider->id}}">{{$provider->name}}</option>
                        @endforeach
                      </select>
                    </div>



                     <button type="submit" class="btn btn-primary mr-2">Registrar</button>
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
{!! Html::script('melody/js/data-table.js') !!}
{!! Html::script('melody/js/dropify.js') !!}

{!! Html::script('melody/js/select2.js') !!}
@endsection
