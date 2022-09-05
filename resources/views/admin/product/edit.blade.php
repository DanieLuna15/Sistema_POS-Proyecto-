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
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">Gestión de Productos</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section"> 
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                  <center>
                    <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task-todo">Editar Producto</button>
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
            <ol class="breadcrumb">
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
                        <h4 class="card-title">Edición de Producto</h4>
                    </div>

                    
                    {!! Form::model($product,['route'=>['products.update',$product], 'method'=>'PUT','files' => true]) !!}
                    <!--'name', 'sell_price','category', 'brand','image',provider',-->

                    <div class="form-group">
                      <label for="name">Nombre Producto: </label>
                      <input type="text" class="form-control" name="name" value="{{$product->name}}" 
                      id="name" aria-describedby="helpId" placeholder="Ingrese el nombre del producto" required>
                    </div>

                    <div class="form-group">
                      <label for="sell_price">Precio de Venta: </label>
                      <input type="number" class="form-control" name="sell_price" value="{{$product->sell_price}}"  
                      id="sell_price" aria-describedby="emailHelpId" placeholder="Precio de venta" required>
                    </div>

                    <div class="form-group">
                      <label for="category_id">Categoría:</label>
                      <select class="form-control" name="category_id" id="category_id">
                        @foreach($categories as $category)
                        <option value="{{$category->id}}" 
                          @if ($category->id==$product->category_id)
                            selected
                          @endif
                          >{{$category->name}}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="brand_id">Marca:</label>
                      <select class="form-control" name="brand_id" id="brand_id">
                        @foreach($brands as $brand)
                        <option value="{{$brand->id}}"
                          @if ($brand->id==$product->brand_id)
                            selected
                          @endif
                          >{{$brand->name}}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="card-body">
                      <h4 class="card-title d-flex">Imagen de Producto
                        <small class="ml-auto align-self-end">
                          <a href="dropify.html" class="font-weight-light" target="_blank">Seleccionar Archivo:</a>  
                        </small>
                      </h4>
                      <input type="file"  name="picture" id="picture" class="dropify"  />


                    <div class="form-group">
                      <label for="provider_id">Proveedor:</label>
                      <select class="form-control" name="provider_id" id="provider_id">
                        @foreach($providers as $provider)
                        <option value="{{$provider->id}}" 
                          @if ($provider->id==$product->provider_id)
                            selected
                          @endif
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
@endsection