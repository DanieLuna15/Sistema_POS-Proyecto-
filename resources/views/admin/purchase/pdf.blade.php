<!DOCTYPE>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Nota de Compra</title>
<style>
    body {
        /*position: relative;*/
        /*width: 16cm;  */
        /*height: 29.7cm; */
        /*margin: 0 auto; */
        /*color: #555555;*/
        /*background: #FFFFFF; */
        font-family: Arial, sans-serif;
        font-size: 14px;
        /*font-family: SourceSansPro;*/
    }
    #datos {
        float: left;
        margin-top: 0%;
        margin-left: 2%;
        margin-right: 2%;
        text-align: left;
    }
    #encabezado {
        text-align: center;
        margin-left: 35%;
        margin-right: 35%;
        font-size: 15px;
    }
    #fact {
        /*position: relative;*/
        float: right;
        margin-top: 2%;
        margin-left: 2%;
        margin-right: 2%;
        font-size: 18px;
        color: #FFFFFF;
        background: #33AFFF;
    }
    #est {
        /*position: relative;*/
        float: right;
        margin-top: 2%;
        margin-left: 2%;
        margin-right: 2%;
        font-size: 18px;
        color: #FFFFFF;
        background: #19a77f;
    }
    section {
        clear: left;
    }
    #proveedor {
        text-align: left;
    }
    #faproveedor {
        width: 40%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 15px;
    }
    #fac,
    #fv,
    #fa {
        color: #FFFFFF;
        font-size: 15px;
    }
    #faproveedor thead {
        padding: 20px;
        background: #33AFFF;
        text-align: left;
        border-bottom: 1px solid #FFFFFF;
    }
    #faccomprador {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 15px;
    }
    #faccomprador thead {
        padding: 20px;
        background: #33AFFF;
        text-align: center;
        border-bottom: 1px solid #FFFFFF;
    }
    #facproducto {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 15px;
    }
    #facproducto thead {
        padding: 20px;
        background: #33AFFF;
        text-align: center;
        border-bottom: 1px solid #FFFFFF;
    }
</style>

<body>

    <header>
        {{--  <div id="logo">
            <img src="img/logo.png" alt="" id="imagen">
        </div>  --}}
        <div>
            <table id="datos">
                <thead>
                    <tr>
                        <th id="">Datos del Proveedor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <p id="proveedor">
                                Nombre: {{$purchase->provider->name}}<br>
                                {{--  {{$purchase->provider->document_type}}-COMPRA: {{$purchase->provider->document_number}}<br>
                                {{-- Dirección: {{$purchase->provider->address}}<br> --}}
                                Teléfono: {{$purchase->provider->phone}}<br>
                                Email: {{$purchase->provider->email}}
                            </p>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="fact">
            {{--  <p>{{$purchase->provider->document_type}} COMPRA<br />
                {{$purchase->provider->document_number}}</p>  --}}
            <p>
                Número de compra:
                {{$purchase->id}}
            </p>
        </div>
        <div id="est">
            {{--  <p>{{$purchase->provider->document_type}} COMPRA<br />
                {{$purchase->provider->document_number}}</p>  --}}
            <p>
                Estado:
                {{$purchase->status}}
            </p>
        </div>
    </header>
    <br>


    <br>
    <section>
        <div>
            <table id="faccomprador" style="text-align:center">
                <thead>
                    <tr id="fv">
                        <th>Usuario Comprador</th>
                        <th>Fecha de Compra</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$purchase->user->name}}</td>
                        <td>{{$purchase->purchase_date}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <br>
    <section>
        <div>
            <table id="facproducto" style="text-align:center">
                <thead>
                    <tr id="fa">
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario (Bs.)</th>
                        <th>SubTotal (Bs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($PurchaseDetails as $PurchaseDetail)
                    <tr>
                        <td>	 {{$PurchaseDetail->product->name}}</td>
                        <td>     {{$PurchaseDetail->quantity}} Unidades </td>
                        <td>Bs./ {{$PurchaseDetail->price}}</td>
                        <td>Bs./ {{number_format($PurchaseDetail->quantity*$PurchaseDetail->price,2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="text-align:center">
                    <tr>
                        <th colspan="3">
                            <p align="right">Subtotal:</p>
                        </th>
                        <td>
                            <p align="right">Bs./ {{number_format($subtotal,2)}}<p>
                        </td>
                    </tr>

                    <tr>
                        <th colspan="3">
                            <p align="right">Total Impuesto ({{$purchase->tax}}%):</p>
                        </th>
                        <td>
                            <p align="right"><span id="total_impuesto">Bs./ {{number_format($subtotal*$purchase->tax/100,2)}}</span></p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="3">
                            <p align="right">Total a Pagar:</p>
                        </th>
                        <td>
                            <p align="right">Bs./ {{number_format($purchase->total,2)}}<p>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
    <br>
    <br>
    <footer>
        <!--puedes poner un mensaje aqui-->
        <div id="datos">
            <p id="encabezado">
                {{--  <b>{{$company->name}}</b><br>{{$company->description}}<br>Telefono:{{$company->telephone}}<br>Email:{{$company->email}}  --}}
            </p>
        </div>
    </footer>
</body>

</html>
