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
        /*text-align: justify;*/
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
        font-size: 20px;
        color: #FFFFFF;
        background: #D2691E;
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
                        <th id="">DATOS DEL VENDEDOR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <p id="proveedor">
                                Nombre: {{$sale->user->name}}<br>

                                Email: {{$sale->user->email}}
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
    </header>
    <br>
    <br>
    <section>
        <div>
            <table id="facproducto" style="text-align:center">
                <thead>
                    <tr id="fa">
                        <th>CANTIDAD</th>
                        <th>PRODUCTO</th>
                        <th>PRECIO VENTA(BS)</th>
                        <th>DESCUENTO(%)</th>
                        <th>SUBTOTAL(BS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($SaleDetails as $SaleDetail)
                    <tr>
                        <td>{{$SaleDetail->quantity}} Unidades</td>
                        <td>{{$SaleDetail->product->name}}</td>
                        <td>Bs./ {{$SaleDetail->price}}</td>
                        <td>{{$SaleDetail->discount}}</td>
                        <td>Bs./ {{number_format($SaleDetail->quantity*$SaleDetail->price - $SaleDetail->quantity*$SaleDetail->price*$SaleDetail->discount/100,2)}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">
                            <p align="right">Subtotal:</p>
                        </th>
                        <th>
                            <p align="right">Bs./ {{number_format($subtotal,2)}}</p>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <p align="right">Total Descuento (%):</p>
                        </th>
                        <th>
                            <p align="right"><span id="total_descuento">Bs./ {{number_format($descuentototal,2)}}</span></p>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <p align="right">Total a Pagar:</p>
                        </th>
                        <th>
                            <p align="right">Bs./{{number_format($sale->total,2)}}</p>
                        </th>
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
