@extends('layouts.admin')
@section('title','Detalles de Compra')
@section('styles')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Detalles de Compra
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('purchases.index')}}">Compras</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle de Compra: {{$purchase->id}}</li>
            </ol>
        </nav>

    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <br />
                <div class="card-body">
                    <div class="row ">
                        <div class="col-12 col-md-3 text-center">
                            <span>Proveedor: <b> </b></span>
                            <div class="form-group">
                                <strong>{{$purchase->provider->name}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Número Compra: <b></b></span>
                            <div class="form-group">
                                <strong>{{$purchase->id}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Usuario Comprador: <b> </b></span>
                            <div class="form-group">
                                <strong>{{$purchase->user->name}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Fecha Compra: <b> </b></span>
                            <div class="form-group">
                                <strong>{{$purchase->purchase_date}}</strong>
                            </div>
                        </div>
                    </div>
                    <br /> <br />
                    <div class="form-group row ">
                        <h4 class="card-title ml-3">Detalles de la compra N°: {{$purchase->id}}</h4>
                        <div class="table-responsive col-md-12">
                            <table id="detalles" class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio (BS)</th>
                                        <th>Cantidad</th>
                                        <th>SubTotal (BS)</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">
                                            <p align="right">SUBTOTAL:</p>
                                        </th>
                                        <th>
                                            <p align="right">Bs./ {{number_format($subtotal,2)}}</p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="3">
                                            <p align="right">TOTAL IMPUESTO ({{$purchase->tax}}%):</p>
                                        </th>
                                        <th>
                                            <p align="right">Bs./ {{number_format($subtotal*$purchase->tax/100,2)}}</p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="3">
                                            <p align="right">TOTAL:</p>
                                        </th>
                                        <th>
                                            <p align="right">Bs./{{number_format($purchase->total,2)}}</p>
                                        </th>
                                    </tr>

                                </tfoot>
                                <tbody>
                                    @foreach($PurchaseDetails as $PurchaseDetail)
                                    <tr>
                                        <td>{{$PurchaseDetail->product->name }}</td>
                                        <td>Bs./{{$PurchaseDetail->price}}</td>
                                        <td>{{$PurchaseDetail->quantity}} Unidades</td>
                                        <td>Bs./{{number_format($PurchaseDetail->quantity*$PurchaseDetail->price,2)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <a href="{{route('purchases.index')}}" class="btn btn-primary float-right">Regresar</a>
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
