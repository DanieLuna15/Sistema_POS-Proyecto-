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
                <li class="breadcrumb-item"><a href="{{route('home')}}">Panel Principal</a></li>
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

                    <div class="row bg-light py-2 rounded mx-1 mb-3 border">
                        <div class="col-6 col-md-3 text-center border-right">
                            <span class="text-muted small d-block">Condición de Pago:</span>
                            @if(($sale->payment_condition ?? $sale->sale_type) === 'CREDITO')
                                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1"><i class="fas fa-hand-holding-usd mr-1"></i> CRÉDITO</span>
                            @else
                                <span class="badge badge-success font-weight-bold px-2 py-1"><i class="fas fa-money-bill-wave mr-1"></i> CONTADO</span>
                            @endif
                        </div>
                        <div class="col-6 col-md-3 text-center border-right">
                            <span class="text-muted small d-block">Forma de Pago:</span>
                            <strong class="text-dark">{{ $sale->payment_method ?? 'Efectivo' }}</strong>
                        </div>
                        <div class="col-6 col-md-3 text-center border-right">
                            <span class="text-muted small d-block">N° Referencia / Voucher:</span>
                            <span class="font-weight-bold {{ $sale->payment_reference ? 'text-primary' : 'text-muted' }}">
                                {{ $sale->payment_reference ?? 'No aplica' }}
                            </span>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <span class="text-muted small d-block">Modalidad:</span>
                            <span class="badge badge-info">{{ $sale->sale_type }}</span>
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
                                            <p align="right">TOTAL:</p>
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
                                            <p align="right">TOTAL A PAGAR:</p>
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

                    @if($sale->sale_type === 'CREDITO')
                    <hr>
                    <div class="row bg-light p-3 rounded mx-1 mb-4">
                        <div class="col-md-3 text-center">
                            <span class="text-muted small d-block">Tipo de Venta:</span>
                            <span class="badge badge-info p-2 mt-1"><i class="fas fa-hand-holding-usd"></i> VENTA A CRÉDITO</span>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="text-muted small d-block">Cuota Inicial:</span>
                            <strong class="text-dark h6">Bs. {{ number_format($sale->down_payment, 2) }}</strong>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="text-muted small d-block">Saldo Pendiente:</span>
                            <strong class="text-danger h5">Bs. {{ number_format($sale->balance_pending, 2) }}</strong>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="text-muted small d-block">Estado del Crédito:</span>
                            @if($sale->credit_status === 'PAGADO')
                                <span class="badge badge-success p-2 mt-1"><i class="fas fa-check"></i> TOTALMENTE PAGADO</span>
                            @else
                                <span class="badge badge-warning p-2 mt-1 text-dark"><i class="fas fa-clock"></i> CRÉDITO PENDIENTE</span>
                            @endif
                        </div>
                    </div>

                    @if(isset($installments) && $installments->count() > 0)
                    <h5 class="card-title ml-2 mb-2 text-info">
                        <i class="fas fa-calendar-alt mr-1"></i> Cronograma de Cuotas del Crédito
                    </h5>
                    <div class="table-responsive col-md-12 mb-4">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cuota</th>
                                    <th>Vencimiento</th>
                                    <th>Monto Cuota</th>
                                    <th>Monto Pagado</th>
                                    <th>Estado</th>
                                    <th>Acción de Cobro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($installments as $inst)
                                <tr>
                                    <td class="font-weight-bold">Cuota #{{ $inst->installment_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d/m/Y') }}</td>
                                    <td>Bs. {{ number_format($inst->amount, 2) }}</td>
                                    <td>Bs. {{ number_format($inst->paid_amount, 2) }}</td>
                                    <td>
                                        @if($inst->status === 'PAGADO')
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Pagado</span>
                                        @else
                                            <span class="badge badge-warning text-dark"><i class="fas fa-clock"></i> Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($inst->status !== 'PAGADO')
                                        <form action="{{ route('sales.installments.pay', $inst) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="payment_method" value="EFECTIVO">
                                            <input type="hidden" name="paid_amount" value="{{ $inst->amount - $inst->paid_amount }}">
                                            <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('¿Registrar cobro de esta cuota?')">
                                                <i class="fas fa-money-bill-wave"></i> Cobrar
                                            </button>
                                        </form>
                                        @else
                                        <span class="small text-muted">{{ $inst->payment_date ? \Carbon\Carbon::parse($inst->payment_date)->format('d/m/Y') : 'Pagado' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    @elseif($sale->sale_type === 'POS_RAPIDO')
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="fas fa-bolt mr-1"></i> <strong>Venta registrada mediante Caja Rápida (POS Táctil).</strong>
                        Método: <strong>{{ $sale->payment_method ?? 'EFECTIVO' }}</strong>
                        @if($sale->cash_received)
                            | Recibido: <strong>Bs. {{ number_format($sale->cash_received, 2) }}</strong>
                            | Cambio: <strong>Bs. {{ number_format($sale->cash_change, 2) }}</strong>
                        @endif
                    </div>
                    @endif

                </div>
                <div class="card-footer bg-light d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('sales.ticket', $sale) }}" target="_blank" class="btn btn-warning text-dark font-weight-bold mr-2 mb-1">
                            <i class="fas fa-receipt mr-1"></i> Imprimir Ticket 80mm
                        </a>
                        <a href="{{ route('sales.ticket', ['sale' => $sale, 'pdf' => 1]) }}" target="_blank" class="btn btn-outline-warning text-dark mr-2 mb-1">
                            <i class="fas fa-print mr-1"></i> Ticket PDF 80mm
                        </a>
                        <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-danger font-weight-bold mr-2 mb-1">
                            <i class="fas fa-file-pdf mr-1"></i> Ver Nota A4 (PDF)
                        </a>
                        <a href="{{ route('sales.pdf', ['sale' => $sale, 'download' => 1]) }}" class="btn btn-outline-danger mr-2 mb-1">
                            <i class="fas fa-download mr-1"></i> Descargar A4
                        </a>
                    </div>
                    <div>
                        <a href="{{route('sales.index')}}" class="btn btn-secondary mb-1">
                            <i class="fas fa-arrow-left mr-1"></i> Regresar
                        </a>
                    </div>
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
