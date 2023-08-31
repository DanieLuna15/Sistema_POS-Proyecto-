<div class="form-row">
    <div class="form-group col-md-5">
        <div class="form-group">
            <label for="provider_id">Proveedor:</label>
            <select class="js-example-basic-single w-responsive form-control" name="provider_id" id="provider_id">
                @foreach($providers as $provider)
                <option value="{{$provider->id}}">{{$provider->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group col-md-5">
        <div class="form-group">
            <label for="product_id">Producto:</label>
            <select class="js-example-basic-single w-responsive form-control" name="product_id" id="product_id">
                <option value="0" selected disabled>Seleccione un producto</option>
                @foreach ($products as $product)
                <option value="{{$product->id}}_{{$product->sell_price}}">
                    {{$product->name}} - ({{$product->stock}} Unidades Actualmente.)
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group col-md-2">
        <label for="tax">Porcentaje de Impuesto:</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text bg-primary text-white">%</span>
            </div>
            <input type="number" class="form-control" name="tax" id="tax" value="0" aria-describedby="basic-addon3">
        </div>
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-4">
        <div class="form-group">
            <label for="quantity">Cantidad a Comprar:</label>
            <div class="input-group">
                <input type="number" name="quantity" id="quantity" class="form-control"  aria-describedby="helpId">
                <div class="input-group-append">
                  <span class="input-group-text bg-primary text-white">Unidades</span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-4">
        <div class="form-group">
            <label for="price">Precio de Compra (Unitario):</label>
            <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-primary text-white">Bs.</span>
                </div>
                <input type="number" name="price" id="price" class="form-control" aria-describedby="helpId">
                <div class="input-group-append">
                  <span class="input-group-text bg-primary text-white">.00</span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-4">
        <div class="form-group">
            <label for="sell_price">Precio de venta Actual (Unitario):</label>
            <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-primary text-white">Bs.</span>
                </div>
                <input type="text" name="sell_price" id="sell_price" value="" class="form-control" aria-describedby="helpId" disabled>
                <div class="input-group-append">
                  <span class="input-group-text bg-primary text-white">.00</span>
                </div>
            </div>
        </div>
    </div>
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
                        <p align="right"><span id="total">Bs./ 0.00</span> </p>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <p align="right">TOTAL IMPUESTO (%):</p>
                    </th>
                    <th>
                        <p align="right"><span id="total_impuesto">Bs./ 0.00</span></p>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
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
