<div class="row">
    <div class="col-md-4 form-group">
        <label for="client_id" class="font-weight-bold">Cliente:</label>
        <select class="js-example-basic-single w-responsive form-control" name="client_id" id="client_id">
            @foreach($clients as $client)
            <option value="{{$client->id}}">{{$client->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label for="payment_condition" class="font-weight-bold">Condición de Pago:</label>
        <select class="form-control" name="payment_condition" id="payment_condition" onchange="onPaymentConditionChange(this.value)">
            <option value="CONTADO" selected>💵 Contado (Pago Inmediato)</option>
            <option value="CREDITO">📅 Crédito (Financiamiento en Cuotas)</option>
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label for="payment_method_id" class="font-weight-bold">Forma de Pago:</label>
        <select class="form-control" name="payment_method_id" id="payment_method_id" onchange="onPaymentMethodChange(this)">
            @foreach($paymentMethods ?? \App\PaymentMethod::active()->get() as $pm)
            <option value="{{ $pm->id }}" 
                    data-condition="{{ $pm->payment_condition }}"
                    data-ref="{{ $pm->requires_reference ? '1' : '0' }}">
                {{ $pm->name }}
            </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-2" id="referenceBoxRow" style="display: none;">
    <div class="col-md-6 form-group">
        <label for="payment_reference" class="font-weight-bold text-info">N° Comprobante / Referencia / Voucher:</label>
        <input type="text" name="payment_reference" id="payment_reference" class="form-control" placeholder="Ej: 9481729 / Lote POS">
    </div>
</div>

<div class="card p-3 mb-3 bg-light border" id="creditParametersBox" style="display: none;">
    <h6 class="font-weight-bold text-info mb-2"><i class="fas fa-hand-holding-usd mr-1"></i> Parámetros de Venta a Crédito</h6>
    <div class="row">
        <div class="col-md-4 form-group">
            <label class="small font-weight-bold">Cuota Inicial (Bs.):</label>
            <input type="number" step="0.5" min="0" name="down_payment" id="down_payment" class="form-control" value="0.00">
        </div>
        <div class="col-md-4 form-group">
            <label class="small font-weight-bold">N° de Cuotas:</label>
            <select name="installments_count" id="installments_count" class="form-control">
                <option value="1">1 Cuota</option>
                <option value="2">2 Cuotas</option>
                <option value="3" selected>3 Cuotas</option>
                <option value="6">6 Cuotas</option>
                <option value="12">12 Cuotas</option>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label class="small font-weight-bold">Frecuencia:</label>
            <select name="installment_frequency" id="installment_frequency" class="form-control">
                <option value="MENSUAL" selected>Mensual</option>
                <option value="QUINCENAL">Quincenal</option>
                <option value="SEMANAL">Semanal</option>
            </select>
        </div>
    </div>
</div>


<div class="form-row">
    <div class="form-group col-md-5">
        <div class="form-group">
            <label for="product_id">Producto:</label>
                {{--  <select class="form-control selectpicker" data-live-search="true" name="product_id" id="product_id">  --}}
            <select class="js-example-basic-single w-responsive form-control" name="product_id" id="product_id">
                <option value="0" disabled selected>Seleccione un producto</option>
                @foreach ($products as $product)
                @php
                    $isUnlimited = $product->isService() || (($activeCompany->business_type ?? '') === 'RESTAURANTE' && $product->stock <= 0);
                    $saleStock = $isUnlimited ? 999999 : $product->stock;
                @endphp
                <option value="{{$product->id}}_{{$saleStock}}_{{$product->sell_price}}">
                    {{$product->name}} {{ $isUnlimited ? '(Plato / Servicio)' : '(Stock: ' . $product->stock . ')' }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group col-md-3">
        <div class="form-group">
            <label for="stock">Stock actual:</label>
            <div class="input-group">
                <input type="text" name="stock" id="stock" value="" class="form-control" disabled>
                <div class="input-group-append">
                  <span class="input-group-text bg-primary text-white">Unidades</span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-4">
        <div class="form-group">
            <label for="price">Precio de Venta (Unitario):</label>
            <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-primary text-white">Bs.</span>
                </div>
                <input require type="number" name="price" id="price" class="form-control" aria-describedby="helpId" disabled>
                <div class="input-group-append">
                  <span class="input-group-text bg-primary text-white">.00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <div class="form-group">
            <label for="quantity">Cantidad a Vender:</label>
            <div class="input-group">
                <input require type="number" name="quantity" id="quantity" class="form-control"  aria-describedby="helpId">
                <div class="input-group-append">
                  <span class="input-group-text bg-primary text-white">Unidades</span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        <label for="discount">Porcentaje de Descuento:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text bg-primary text-white">%</span>
            </div>
            <input type="number" name="discount" id="discount" class="form-control" aria-describedby="helpId" value="0">
        </div>
    </div>
</div>

<div class="form-group">
    <button type="button" id="agregar" class="btn btn-primary float-right">Agregar Producto</button>
</div>

<div class="form-group">
    <h4 class="card-title">Detalles de la Venta:</h4>
    <div class="table-responsive col-md-12">
        <table id="detalles" class="table table-striped">
            <thead>
                <tr>
                    <th>Eliminar</th>
                    <th>Producto</th>
                    <th>Precio de Venta(Bs)</th>
                    <th>Descuento %</th>
                    <th>Cantidad</th>
                    <th>SubTotal(Bs)</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="5">
                        <p align="right">TOTAL:</p>
                    </th>
                    <th>
                        <p align="right"><span id="total">Bs./ 0.00</span> </p>
                    </th>
                </tr>
                <!--
                <tr>
                    <th colspan="5">
                        <p align="right">TOTAL GANANCIAS (%):</p>
                    </th>
                    <th>
                        <p align="right"><span id="total_impuesto">Bs./ 0.00</span></p>
                    </th>
                </tr>
                -->
                <tr>
                    <th colspan="5">
                        <p align="right">TOTAL DESCUENTO (%):</p>
                    </th>
                    <th>
                        <p align="right"><span id="total_descuento">Bs./ 0.00</span></p>
                    </th>
                </tr>

                <tr>
                    <th colspan="5">
                        <p align="right">TOTAL A PAGAR:</p>
                    </th>
                    <th>
                        <p align="right"><span align="right" id="total_pagar_html">Bs./ 0.00</span> <input type="hidden"
                                name="total" id="total_pagar"></p>
                    </th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

