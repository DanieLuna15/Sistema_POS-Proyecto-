@extends('layouts.admin')
@section('title','Detalles de Venta')
@section('styles')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Detalles de Venta
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item"><a href="{{route('sales.index')}}">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detalle de Venta: {{$sale->id}}</li>
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
                            <span>Cliente: <b> </b></span>
                            <div class="form-group">
                                <strong>{{$sale->client->name}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Número Venta: <b></b></span>
                            <div class="form-group">
                                <strong>{{$sale->id}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Usuario Vendedor: <b> </b></span>
                            <div class="form-group">
                                <strong>{{$sale->user->name}}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-center">
                            <span>Fecha Venta: <b> </b></span>
                            <div class="form-group">
                                <strong>{{ Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i:s') }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row ">
                        <h4 class="card-title ml-3">Detalles de la Venta N°: {{$sale->id}}</h4>
                        <div class="table-responsive col-md-12">
                            <table id="detalles" class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio (BS)</th>
                                        <th>Cantidad</th>
                                        <th>Descuento (%)</th>
                                        <th>SubTotal (BS)</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">
                                            <p align="right">SUBTOTAL:</p>
                                        </th>
                                        <th>
                                            <p align="right">Bs./ {{number_format($subtotal,2)}}</p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="4">
                                            <p align="right">TOTAL DESCUENTO (%):</p>
                                        </th>
                                        <th>
                                            <p align="right"><span id="total_descuento">Bs./ {{number_format($descuentototal,2)}}</span></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="4">
                                            <p align="right">TOTAL:</p>
                                        </th>
                                        <th>
                                            <p align="right">Bs./{{number_format($sale->total,2)}}</p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="5">
                                            <p align="right"> SON: {{$totalLiteral}}</p>
                                        </th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach($SaleDetails as $SaleDetail)
                                    <tr>
                                        <td>{{$SaleDetail->product->name }}</td>
                                        <td>Bs./{{$SaleDetail->price}}</td>
                                        <td>{{$SaleDetail->quantity}} Unidades</td>
                                        <td>{{$SaleDetail->discount}}.%</td>
                                        <td>Bs./{{number_format($SaleDetail->quantity*$SaleDetail->price,2)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <a href="{{route('sales.index')}}" class="btn btn-primary float-right">Regresar</a>
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
