@extends('layouts.admin')
@section('title','Registro de Venta')
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
<li class="nav-item d-none d-lg-flex">
    <a class="nav-link" type="button" data-toggle="modal" data-target="#exampleModal-2">
      <span class="btn btn-primary">+ Registrar cliente</span>
    </a>
</li>
@endsection


@section('options')
@endsection

@section('preference')
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Nueva Venta:
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
                <li class="breadcrumb-item"><a href="{{route('sales.index')}}">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Venta</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
            {!! Form::open(['route'=>'sales.store', 'method'=>'POST']) !!}

                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Datos de la Venta:</h4>
                    </div>
                    @include('admin.sale._form')
                </div>
                <div class="card-footer text-muted">
                    <button type="submit" id="guardar" class="btn btn-primary float-right">Registrar</button>
                        <a href="{{route('sales.index')}}" class="btn btn-light">
                            Cancelar
                    </a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal-2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2"
    aria-hidden="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel-2">Registro rápido de cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['route'=>'clients.store', 'method'=>'POST','files' => true]) !!}

                <div class="modal-body">

                    <div class="form-group">
                        <label for="name">* Nombre: </label>
                        <input autofocus type="text"class="form-control @error('name') is-invalid @enderror" name="name"
                        id=name" aria-describedby="helpId" placeholder="Ingrese el nombre del Cliente"  value="{{old('name')}}">
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('name')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="ci">* Cédula de Identidad: </label>
                        <input type="number" class="form-control @error('ci') is-invalid @enderror" name="ci"
                        id=ci" aria-describedby="helpId" placeholder="Ingrese el número de Carnet"  value="{{old('ci')}}">
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('ci')}}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="phone">* Teléfono/Celular: </label>
                        <input type="number" class="form-control @error('phone') is-invalid @enderror" name="phone"
                        id=phone" aria-describedby="helpId" placeholder="Ingrese el número de telefono/celular"  value="{{old('phone')}}">
                        <span class="invalid-feedback" role="alert">
                            <strong>{{$errors-> first('phone')}}</strong>
                        </span>
                    </div>

                    <input type="hidden" name="sale" value="1">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Registrar</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection
@section('scripts')
{!! Html::script('melody/js/alerts.js') !!}
{!! Html::script('melody/js/avgrund.js') !!}

{!! Html::script('melody/js/select2.js') !!}
{!! Html::script('js/sweetalert2.all.min.js') !!}

<script>
    $(document).ready(function () {
        $("#agregar").click(function () {
            agregar();
        });
    });
    var cont = 1;
    total = 0;
    subtotal = [];
    $("#guardar").hide();
    $("#product_id").change(mostrarValores);

    function mostrarValores(){
        datosProducto=document.getElementById('product_id').value.split('_');
        $("#price").val(datosProducto[2]);
        $("#stock").val(datosProducto[1]);
    }

    function agregar() {
        datosProducto=document.getElementById('product_id').value.split('_');
        product_id = datosProducto[0];
        product = $("#product_id option:selected").text();
        quantity = $("#quantity").val();
        discount = $("#discount").val();
        price = $("#price").val();
        stock = $("#stock").val();
        if (parseInt(product_id) > 0) {
            if (product_id != "" && product_id != 0 && quantity != "" && discount != "" && price != "") {
                if (parseInt(quantity) > 0 && quantity % 1 == 0) {
                    if (parseInt(stock) >= parseInt(quantity)) {
                        if (parseInt(discount) >= 0 && parseInt(discount) <= 50) {
                            subtotal[cont] = (quantity * price) - (quantity * price * discount / 100);
                            total = total + subtotal[cont];
                            total_descuento =0;
                            var fila = '<tr class="selected" id="fila' + cont + '"><td><button type="button" class="btn btn-danger btn-sm" onclick="eliminar(' + cont + ');"><i class="fa fa-times fa-2x"></i></button></td> <td><input type="hidden" name="product_id[]" value="' + product_id + '">' + product + '</td> <td> <input type="hidden" name="price[]" value="' + parseFloat(price).toFixed(2) + '"> <input class="form-control" type="number" value="' + parseFloat(price).toFixed(2) + '" disabled> </td> <td> <input type="hidden" name="discount[]" value="' + parseFloat(discount) + '"> <input class="form-control" type="number" value="' + parseFloat(discount) + '" disabled> </td> <td> <input type="hidden" name="quantity[]" value="' + quantity + '"> <input type="number" value="' + quantity + '" class="form-control" disabled> </td> <td align="right">Bs/' + parseFloat(subtotal[cont]).toFixed(2) + '</td></tr>';
                            cont++;
                            limpiar();
                            totales();
                            evaluar();
                            $('#detalles').append(fila);
                        }   else {
                            Swal.fire({
                                type: 'warning',
                                text: 'El porcentaje de descuento debe ser mayor o igual a cero y no puede exceder del 50%.',
                            })
                        }
                    }   else {
                        Swal.fire({
                            type: 'warning',
                            text: 'La cantidad a vender supera el stock.',
                        })
                    }
                }   else {
                    Swal.fire({
                        type: 'warning',
                        text: 'La Cantidad debe ser mayor a 0 y un número entero.',
                    })
                }
            }   else {
                Swal.fire({
                    type: 'error',
                    text: 'Rellene todos los campos del detalle de la venta.',
                })
            }
        }   else {
            Swal.fire({
                    type: 'warning',
                    text: 'Primero debes elegir un Producto.',
            })
        }
    }
    function limpiar() {
        $("#stock").val(stock-quantity);
        $("#quantity").val("");
        $("#discount").val("0");
    }
    function totales() {
        $("#total").html("BS/ " + total.toFixed(2));

        total_descuento = (quantity * price * discount / 100);
        total_pagar = total;

        $("#total_descuento").html("BS/ " + total_descuento.toFixed(2));

        $("#total_pagar_html").html("BS/ " + total_pagar.toFixed(2));
        $("#total_pagar").val(total_pagar.toFixed(2));
    }
    function evaluar() {
        if (total > 0) {
            $("#guardar").show();
        } else {
            $("#guardar").hide();
        }
    }
    function eliminar(index) {
        total = total - subtotal[index];

        total_descuento = (quantity * price * discount / 100);

        total_pagar_html = total;
        
        $("#total").html("BS" + total);
        $("#total_descuento").html("BS/ " + total_descuento);
        $("#total_pagar_html").html("BS/ " + total_pagar_html);
        $("#total_pagar").val(total_pagar_html.toFixed(2));
        $("#fila" + index).remove();
        evaluar();
    }
</script>

@endsection
