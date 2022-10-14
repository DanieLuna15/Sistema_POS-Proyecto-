<!DOCTYPE>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Nota de Venta</title>
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
        font-size: 55px;
    }
    #fact {
        /*position: relative;*/
        float: right;
        margin-top: 2%;
        margin-left: 2%;
        margin-right: 2%;
        font-size: 18px;
        color: #FFFFFF;
        background: #D2691E;
    }
    #est {
        /*position: relative;*/
        float: right;
        margin-top: 2%;
        margin-left: 2%;
        margin-right: 2%;
        font-size: 18px;
        color: #FFFFFF;
        background: #d1b441;
    }
    section {
        clear: left;
    }
    #cliente {
        text-align: left;
    }
    #facliente {
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
    #facliente thead {
        padding: 20px;
        background: #D2691E;
        text-align: left;
        border-bottom: 1px solid #FFFFFF;
    }
    #facvendedor {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 15px;
    }
    #facvendedor thead {
        padding: 20px;
        background: #D2691E;
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
        background: #D2691E;
        text-align: center;
        border-bottom: 1px solid #FFFFFF;
    }
</style>

<body>
    <header>
        {{--  <div id="logo">
            <img src="{{asset($company->logo)}}" alt="" id="imagen">
        </div>  --}}
        <div>
            <table id="datos">
                <thead>
                    <tr>
                        <th id="">Datos del Cliente</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <p id="cliente">
                                Nombre: {{$sale->client->name}}<br>
                                CI: {{$sale->client->ci}}<br>
                                Teléfono: {{$sale->client->phone}}
                            </p>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="fact">
            {{--  <p>
                {{$sale->user->types_identification}}-VENTA
                <br>
                {{$sale->user->id}}
            </p>  --}}
            <p>
                Número de Venta:
                {{$sale->id}}
            </p>
        </div>
        <div id="est">
            {{--  <p>{{$purchase->provider->document_type}} COMPRA<br />
                {{$purchase->provider->document_number}}</p>  --}}
            <p>
                Estado:
                {{$sale->status}}
            </p>
        </div>
    </header>
    <br>

    <br>
    <section>
        <div>
            <table id="facvendedor" style="text-align:center">
                <thead>
                    <tr id="fv">
                        <th>Usuario Vendedor</th>
                        <th>Fecha de Venta</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$sale->user->name}}</td>
                        <td>{{$sale->sale_date}}</td>
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
                        <th>Precio Unitario(Bs.)</th>
                        <th>Descuento(%)</th>
                        <th>SubTotal(Bs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($SaleDetails as $SaleDetail)
                    <tr>
                        <td>{{$SaleDetail->product->name}}</td>
                        <td>{{$SaleDetail->quantity}} Unidades</td>
                        <td>Bs./ {{$SaleDetail->price}}</td>
                        <td>{{$SaleDetail->discount}}.%</td>
                        <td>Bs./ {{number_format($SaleDetail->quantity*$SaleDetail->price - $SaleDetail->quantity*$SaleDetail->price*$SaleDetail->discount/100,2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="text-align:center">
                    <tr>
                        <th colspan="4">
                            <p align="right">Subtotal:</p>
                        </th>
                        <td>
                            <p align="right">Bs./ {{number_format($subtotal,2)}}</p>
                        </td>
                    </tr>

                    <tr>
                        <th colspan="4">
                            <p align="right">Total Descuento:</p>
                        </th>
                        <td>
                            <p align="right"><span id="total_descuento">Bs./ {{number_format($descuentototal,2)}}</span></p>
                        </td>
                    </tr>

                    <tr>
                        <th colspan="4">
                            <p align="right">Total a Pagar:</p>
                        </th>
                        <td>
                            <p align="right">Bs./{{number_format($sale->total,2)}}</p>
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
