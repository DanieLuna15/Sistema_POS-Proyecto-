@extends('layouts.admin')
@section('title', 'Nueva Cotización / Proforma')

@section('content')
@php $sym = $activeCompany->currency_symbol ?? 'Bs.'; @endphp
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title mb-1">
                <i class="fas fa-file-contract text-primary mr-2"></i> Emitir Nueva Cotización / Proforma
            </h3>
            <small class="text-muted">Las cotizaciones no descuentan stock hasta que el cliente las apruebe y se conviertan en venta.</small>
        </div>
        <a href="{{ route('quotations.index') }}" class="btn btn-light">
            <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
        </a>
    </div>

    <form action="{{ route('quotations.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 form-group">
                                <label class="font-weight-bold">Cliente *</label>
                                <select name="client_id" class="js-example-basic-single w-100 form-control" required>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->ci ?? $client->nit ?? 'S/N' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold">Validez de la Oferta (Días)</label>
                                <input type="number" name="valid_days" class="form-control" value="15" min="1">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Observaciones / Tiempo de Entrega</label>
                                <input type="text" name="notes" class="form-control" placeholder="Ej: Entrega inmediata, precios incluyen garantía...">
                            </div>
                        </div>

                        <hr>
                        <div class="row align-items-end mb-3">
                            <div class="col-md-5 form-group mb-0">
                                <label class="font-weight-bold">Seleccionar Producto o Servicio</label>
                                <select id="q_product" class="js-example-basic-single w-100 form-control">
                                    <option value="" disabled selected>-- Buscar ítem por nombre o código --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}_{{ $product->sell_price }}_{{ $product->stock }}_{{ $product->unit_measure ?? 'UND' }}_{{ $product->item_type ?? 'PRODUCTO' }}">
                                            [{{ $product->code }}] {{ $product->name }} ({{ $sym }} {{ number_format($product->sell_price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 form-group mb-0">
                                <label class="font-weight-bold">Cantidad</label>
                                <input type="number" id="q_qty" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-2 form-group mb-0">
                                <label class="font-weight-bold">Precio Unit. ({{ $sym }})</label>
                                <input type="number" step="0.01" id="q_price" class="form-control" placeholder="0.00">
                            </div>
                            <div class="col-md-1 form-group mb-0">
                                <label class="font-weight-bold">Desc %</label>
                                <input type="number" step="0.1" id="q_disc" class="form-control" value="0" min="0" max="100">
                            </div>
                            <div class="col-md-2 form-group mb-0">
                                <button type="button" id="btn_add_q" class="btn btn-primary btn-block">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered" id="q_table">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Producto / Servicio</th>
                                        <th style="width: 120px;">Cantidad</th>
                                        <th style="width: 140px;">Precio Unit. ({{ $sym }})</th>
                                        <th style="width: 110px;">Desc. (%)</th>
                                        <th style="width: 150px;" class="text-right">Subtotal ({{ $sym }})</th>
                                        <th style="width: 70px;">Quitar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">TOTAL COTIZACIÓN:</th>
                                        <th class="text-right text-primary" style="font-size: 18px;" id="q_total_label">{{ $sym }} 0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="form-group mt-3">
                            <label class="font-weight-bold">Condiciones Comerciales / Garantía</label>
                            <textarea name="terms" class="form-control" rows="2">{{ $company->terms_conditions ?? 'Cotización sujeta a disponibilidad de stock. Precios expresados en ' . ($company->currency_name ?? 'Bolivianos') . '.' }}</textarea>
                        </div>

                        <div class="text-right mt-3">
                            <button type="submit" id="btn_save_q" class="btn btn-success btn-lg" disabled>
                                <i class="fas fa-check-circle mr-1"></i> Guardar y Generar Proforma PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
{!! Html::script('melody/js/select2.js') !!}
<script>
    var qTotal = 0;
    var qRowId = 0;

    $('#q_product').on('change', function() {
        var val = $(this).val();
        if (val) {
            var parts = val.split('_');
            $('#q_price').val(parts[1]);
        }
    });

    $('#btn_add_q').on('click', function() {
        var val = $('#q_product').val();
        if (!val) {
            Swal.fire('Atención', 'Seleccione un producto o servicio de la lista.', 'warning');
            return;
        }
        var parts = val.split('_');
        var prodId = parts[0];
        var prodName = $('#q_product option:selected').text();
        var qty = parseFloat($('#q_qty').val()) || 1;
        var price = parseFloat($('#q_price').val()) || 0;
        var disc = parseFloat($('#q_disc').val()) || 0;

        if (qty <= 0 || price <= 0) {
            Swal.fire('Atención', 'Cantidad y precio deben ser mayores a 0.', 'warning');
            return;
        }

        var sub = (qty * price) - ((qty * price * disc) / 100);
        qRowId++;

        var row = '<tr id="qrow_' + qRowId + '" data-sub="' + sub + '">' +
            '<td><input type="hidden" name="product_id[]" value="' + prodId + '">' + prodName + '</td>' +
            '<td><input type="hidden" name="quantity[]" value="' + qty + '">' + qty + '</td>' +
            '<td><input type="hidden" name="price[]" value="' + price + '">' + price.toFixed(2) + '</td>' +
            '<td><input type="hidden" name="discount[]" value="' + disc + '">' + disc + '%</td>' +
            '<td class="text-right font-weight-bold">' + sub.toFixed(2) + '</td>' +
            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeQRow(' + qRowId + ')"><i class="fas fa-times"></i></button></td>' +
            '</tr>';

        $('#q_table tbody').append(row);
        recalcQTotal();
    });

    function removeQRow(id) {
        $('#qrow_' + id).remove();
        recalcQTotal();
    }

    function recalcQTotal() {
        var sum = 0;
        var count = 0;
        $('#q_table tbody tr').each(function() {
            sum += parseFloat($(this).attr('data-sub')) || 0;
            count++;
        });
        $('#q_total_label').text('{{ $sym }} ' + sum.toFixed(2));
        $('#btn_save_q').prop('disabled', count === 0);
    }
</script>
@endsection

