<div class="form-group">
    <label for="provider_id">Proveedor:</label>
        <select class="js-example-basic-single w-100" name="provider_id" id="provider_id">
            @foreach($providers as $provider)
            <option value="{{$provider->id}}">{{$provider->name}}</option>
            @endforeach
        </select>
</div>


<div class="form-group">
  <label for="tax">Porcentaje de Impuesto:</label>
  <input type="number" required name="tax" id="tax" class="form-control" value="0" placeholder="Ejemplo: 18%" aria-describedby="helpId">
</div>

<div class="form-group">
    <label for="product_id">Producto:</label>
        {{--  <select class="form-control selectpicker" data-live-search="true" name="product_id" id="product_id">  --}}
    <select class="js-example-basic-single w-100" name="product_id" id="product_id" required>
        <option value="0" selected disabled>Seleccione un producto</option>
        @foreach ($products as $product)
        <option value="{{$product->id}}">{{$product->name}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
  <label for="quantity">Cantidad:</label>
  <input type="number" name="quantity" id="quantity" class="form-control"  aria-describedby="helpId">
</div>

<div class="form-group">
  <label for="price">Precio de Compra:</label>
  <input type="number" name="price" id="price" class="form-control" aria-describedby="helpId">
</div>

<!--<div class="form-group">
    <label for="sell_price">Precio de venta Actual:</label>
    <input type="number" name="sell_price" id="sell_price" value="{{$product->sell_price}}" class="form-control" aria-describedby="helpId" disabled>
</div>-->

<div class="form-group">
    <button type="button" id="agregar" class="btn btn-primary float-right">Agregar Producto</button>
</div>

<div class="form-group">
    <h4 class="card-title">Detalles de Compra:</h4>
    <div class="table-responsive col-md-12">
        <table id="detalles" class="table table-striped">
            <thead>
                <tr>
                    <th>Eliminar</th>
                    <th>Producto</th>
                    <th>Precio(Bs)</th>
                    <th>Cantidad</th>
                    <th>SubTotal(Bs)</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="4">
                        <p align="right">TOTAL:</p>
                    </th>
                    <th>
                        <p align="right"><span id="total">Bs 0.00</span> </p>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <p align="right">TOTAL IMPUESTO (%):</p>
                    </th>
                    <th>
                        <p align="right"><span id="total_impuesto">Bs 0.00</span></p>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <p align="right">TOTAL PAGAR:</p>
                    </th>
                    <th>
                        <p align="right"><span align="right" id="total_pagar_html">Bs 0.00</span> <input type="hidden"
                                name="total" id="total_pagar"></p>
                    </th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
