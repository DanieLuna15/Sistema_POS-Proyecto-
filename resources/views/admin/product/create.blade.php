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
@endsection

@section('preference')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Nuevo Producto:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{route('products.index')}}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Producto</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!--<div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Datos del Producto:</h4>

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

                <form class="form-sample">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nombre Producto: </label>
                        <div class="col-sm-9">
                            <input autofocus type="text" class="form-control" name="name"
                            id="name" aria-describedby="helpId"  value="{{old('name')}}" placeholder="Nombre del producto">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Last Name</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Gender</label>
                        <div class="col-sm-9">
                          <select class="form-control">
                            <option>Male</option>
                            <option>Female</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Date of Birth</label>
                        <div class="col-sm-9">
                          <input class="form-control" placeholder="dd/mm/yyyy"/>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Category</label>
                        <div class="col-sm-9">
                          <select class="form-control">
                            <option>Category1</option>
                            <option>Category2</option>
                            <option>Category3</option>
                            <option>Category4</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Membership</label>
                        <div class="col-sm-4">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="membershipRadios" id="membershipRadios1" value="" checked>
                              Free
                            </label>
                          </div>
                        </div>
                        <div class="col-sm-5">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="membershipRadios" id="membershipRadios2" value="option2">
                              Professional
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <p class="card-description">
                    Address
                  </p>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Address 1</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" />
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">State</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Address 2</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" />
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Postcode</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">City</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" />
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Country</label>
                        <div class="col-sm-9">
                          <select class="form-control">
                            <option>America</option>
                            <option>Italy</option>
                            <option>Russia</option>
                            <option>Britain</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
                <button type="submit" class="btn btn-primary mr-2">Registrar</button>
                <a href="{{route('products.index')}}" class="btn btn-light">
                   Cancelar
                </a>
                {!! Form::close() !!}

              </div>
            </div>
        </div>-->
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos del Producto:</h4>
                    </div>

                    {!! Form::open(['route'=>'products.store', 'method'=>'POST','files' => true]) !!}

                    <div class="form-group">
                        <label for="name">Nombre Producto: </label>
                        <input autofocus type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        id="name" aria-describedby="helpId"  value="{{old('name')}}" placeholder="Nombre del producto">
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('name')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="sell_price">Precio de Venta: </label>
                        <input type="number" class="form-control @error('sell_price') is-invalid @enderror"  name="sell_price"
                        id="sell_price" aria-describedby="emailHelpId" value="{{old('sell_price')}}" placeholder="Precio de venta" >
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('sell_price')}}</strong>
                        </span>
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

                    <div class="card-body">
                      <h4 class="card-title d-flex">Imagen de Producto
                        <small class="ml-auto align-self-end">

                        </small>
                      </h4>
                      <input accept="image/*" type="file" name="picture" id="picture" class="dropify" required/>
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
