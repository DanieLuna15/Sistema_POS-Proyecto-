@extends('layouts.admin')
@section('title', 'Detalle de Cotización #' . $quotation->id)

@section('content')
@php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-file-contract text-primary mr-2"></i> Cotización / Proforma #COT-{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}
            </h3>
            <small class="text-muted">Cliente: <strong>{{ $quotation->client->name }}</strong> &bull; Fecha: {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d/m/Y H:i') }}</small>
        </div>
        <div>
            <a href="{{ route('quotations.pdf', $quotation) }}" target="_blank" class="btn btn-danger mr-2">
                <i class="fas fa-file-pdf mr-1"></i> Imprimir Proforma A4 (PDF)
            </a>
            @if($quotation->status !== 'CONVERTIDA')
                <button type="button" class="btn btn-success font-weight-bold mr-2" data-toggle="modal" data-target="#convertModal">
                    <i class="fas fa-shopping-cart mr-1"></i> Convertir en Venta Ahora (1 Clic)
                </button>
            @else
                <a href="{{ route('sales.show', $quotation->converted_sale_id) }}" class="btn btn-info mr-2">
                    <i class="fas fa-external-link-alt mr-1"></i> Ver Venta Generada #{{ $quotation->converted_sale_id }}
                </a>
            @endif
            <a href="{{ route('quotations.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-primary">Datos del Cliente</h5>
                    <p class="mb-1"><strong>Señor(es):</strong> {{ $quotation->client->name }}</p>
                    <p class="mb-1"><strong>NIT / CI:</strong> {{ $quotation->client->ci ?? $quotation->client->nit ?? 'S/N' }}</p>
                    <p class="mb-1"><strong>Teléfono:</strong> {{ $quotation->client->phone ?? '-' }}</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <h5 class="font-weight-bold text-primary">Información de la Oferta</h5>
                    <p class="mb-1"><strong>Válida hasta:</strong> {{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') : '-' }}</p>
                    <p class="mb-1"><strong>Elaborado por:</strong> {{ $quotation->user->name }}</p>
                    <p class="mb-1"><strong>Estado:</strong> <span class="badge badge-info">{{ $quotation->status }}</span></p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Producto / Servicio</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Precio Unit.</th>
                            <th class="text-center">Desc. (%)</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->details as $idx => $det)
                        @php
                            $lineSub = ($det->quantity * $det->price) - (($det->quantity * $det->price * $det->discount) / 100);
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $det->product->code ?? '-' }}</td>
                            <td><strong>{{ $det->product->name ?? 'Ítem' }}</strong></td>
                            <td class="text-center">{{ $det->quantity }} {{ $det->product->unit_measure ?? 'UND' }}</td>
                            <td class="text-right">{{ $sym }} {{ number_format($det->price, 2) }}</td>
                            <td class="text-center">{{ $det->discount }}%</td>
                            <td class="text-right font-weight-bold">{{ $sym }} {{ number_format($lineSub, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">TOTAL COTIZADO:</th>
                            <th class="text-right text-primary" style="font-size: 18px;">{{ $sym }} {{ number_format($quotation->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-3 p-3 bg-light rounded border">
                <strong>Son:</strong> {{ $totalLiteral }}<br>
                @if($quotation->notes)
                    <strong>Observaciones:</strong> {{ $quotation->notes }}<br>
                @endif
                @if($quotation->terms)
                    <strong>Condiciones:</strong> {{ $quotation->terms }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Convertir Cotización en Venta -->
<div class="modal fade" id="convertModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('quotations.convert', $quotation) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle mr-2"></i> Aprobar y Convertir en Venta Confirmada</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Al confirmar, el sistema creará la venta por <strong>{{ $sym }} {{ number_format($quotation->total, 2) }}</strong>, descontará el stock correspondiente del Kardex y emitirá el comprobante oficial.</p>
                    <div class="form-group">
                        <label class="font-weight-bold">Forma de Pago *</label>
                        <select name="payment_method_id" class="form-control" required>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">N° Referencia / Comprobante (Opcional)</label>
                        <input type="text" name="payment_reference" class="form-control" value="COT-{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success font-weight-bold">Confirmar Venta Ahora</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

