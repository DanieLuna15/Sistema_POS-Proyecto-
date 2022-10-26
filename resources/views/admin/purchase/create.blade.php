@extends('layouts.admin')
@section('title','Registro de Compra')
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
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">Gestión de Compras</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                    <center>
                        <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task-todo">Nueva Compra</button>
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
            Compras
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('purchases.index')}}">Compras</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registro de Compra</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
            {!! Form::open(['route'=>'purchases.store', 'method'=>'POST']) !!}

                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Registro de Compra:</h4>
                    </div>
                    @include('admin.purchase._form')
                </div>
                <div class="card-footer text-muted">
                    <button type="submit" id="guardar" class="btn btn-primary float-right">Registrar</button>
                        <a href="{{route('purchases.index')}}" class="btn btn-light">
                            Cancelar
                    </a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
{!! Html::script('melodyjs/alerts.js') !!}
{!! Html::script('melodyjs/avgrund.js') !!}

{!! Html::script('melody/js/select2.js') !!}
{!! Html::script('js/sweetalert2.all.min.js') !!}

<script>
    $(document).ready(function () {
        $("#agregar").click(function () {
            agregar();
        });
    });

    var cont = 0;
    total = 0;
    subtotal = [];

    $("#guardar").hide();



    function agregar() {

        product_id = $("#product_id").val();
        product = $("#product_id option:selected").text();
        quantity = $("#quantity").val();
        price = $("#price").val();
        tax = $("#tax").val();

        if (product_id != "" && product_id != 0 && quantity != "" && tax != "" && price != "") {
            if (parseInt(product_id) > 0) {
                if (parseInt(quantity) > 0 && quantity % 1 == 0) {
                    if (parseInt(price) > 0) {
                        if (parseInt(tax) >= 0 && parseInt(tax) <= 18) {
                            subtotal[cont] = quantity * price;
                            total = total + subtotal[cont];
                            var fila = '<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-danger btn-sm" onclick="eliminar('+cont+');"><i class="fa fa-times"></i></button></td> <td><input type="hidden" name="product_id[]" value="'+product_id+'">'+product+'</td> <td> <input type="hidden" id="price[]" name="price[]" value="' + price + '"> <input class="form-control" type="number" id="price[]" value="' + price + '" disabled> </td>  <td> <input type="hidden" name="quantity[]" value="' + quantity + '"> <input class="form-control" type="number" value="' + quantity + '" disabled> </td> <td align="right">Bs./ ' + subtotal[cont] + ' </td></tr>';
                            cont++;
                            limpiar();
                            totales();
                            evaluar();
                            $('#detalles').append(fila);
                        }   else {
                            Swal.fire({
                                type: 'warning',
                                text: 'El porcentaje de Impuesto debe ser mayor o igual a cero y no puede exceder del 18% (IVA).',
                            })
                        }
                    }   else {
                        Swal.fire({
                            type: 'warning',
                            text: 'El precio de compra debe ser mayor a 0.',
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
                    type: 'warning',
                    text: 'Primero debes elegir un Producto.',
                })
            }
        }   else {
            Swal.fire({
                type: 'error',
                text: 'Rellene todos los campos del detalle de la compra.',
            })
        }
    }

    function limpiar() {
        $("#quantity").val("");
        $("#price").val("");
    }

    function totales() {
        $("#total").html("Bs. " + total.toFixed(2));
        total_impuesto = total * tax / 100;
        total_pagar = total + total_impuesto;
        $("#total_impuesto").html("Bs. " + total_impuesto.toFixed(2));
        $("#total_pagar_html").html("Bs. " + total_pagar.toFixed(2));
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
        total_impuesto = total * tax / 100;
        total_pagar_html = total + total_impuesto;
        $("#total").html("Bs." + total);
        $("#total_impuesto").html("Bs." + total_impuesto);
        $("#total_pagar_html").html("Bs." + total_pagar_html);
        $("#total_pagar").val(total_pagar_html.toFixed(2));
        $("#fila" + index).remove();
        evaluar();
    }

</script>

@endsection
