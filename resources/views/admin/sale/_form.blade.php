<div class="form-group">
    <label for="client_id">Cliente:</label>
        <select class="form-control" name="client_id" id="client_id">
            @foreach($clients as $client)
            <option value="{{$client->id}}">{{$client->name}}</option>
            @endforeach
        </select>
</div>




<div class="form-group">
    <label for="product_id">Producto:</label>
        {{--  <select class="form-control selectpicker" data-live-search="true" name="product_id" id="product_id">  --}}
    <select class="form-control" name="product_id" id="product_id">
        <option value="0" disabled selected>Seleccione un producto</option>
        @foreach ($products as $product)
        <option value="{{$product->id}}_{{$product->stock}}_{{$product->sell_price}}">{{$product->name}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
  <label for="stock"> Stock Actual:</label>
  <input type="text" name="stock" id="stock" value="0" class="form-control" disabled>
</div>


<div class="form-group">
  <label for="quantity">Cantidad:</label>
  <input require type="number" name="quantity" id="quantity" class="form-control"  aria-describedby="helpId">
</div>

<div class="form-group">
  <label for="price">Precio de Venta:</label>
  <input require type="number" name="price" id="price" class="form-control" aria-describedby="helpId" disabled>
</div>

<div class="form-group">
  <label for="tax">Porcentaje de Ganancia (%):</label>
  <input require type="number" require name="tax" id="tax" class="form-control" placeholder="Ejemplo: 18%" value="0" aria-describedby="helpId">
</div>

<div class="form-group">
  <label for="discount">Porcentaje de Descuento (%):</label>
  <input require type="number" name="discount" id="discount" class="form-control" aria-describedby="helpId" value="0">
</div>



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
                        <p align="right"><span id="total">Bs/ 0.00</span> </p>
                    </th>
                </tr>
                <tr>
                    <th colspan="5">
                        <p align="right">TOTAL GANANCIAS (%):</p>
                    </th>
                    <th>
                        <p align="right"><span id="total_impuesto">Bs/ 0.00</span></p>
                    </th>
                </tr>
                <!--
                <tr>
                    <th colspan="5">
                        <p align="right">TOTAL DESCUENTO (%):</p>
                    </th>
                    <th>
                        <p align="right"><span id="total_descuento">Bs/ 0.00</span></p>
                    </th>
                </tr>
                -->
                <tr>
                    <th colspan="5">
                        <p align="right">TOTAL PAGAR:</p>
                    </th>
                    <th>
                        <p align="right"><span align="right" id="total_pagar_html">Bs/ 0.00</span> <input type="hidden"
                                name="total" id="total_pagar"></p>
                    </th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>
</div>