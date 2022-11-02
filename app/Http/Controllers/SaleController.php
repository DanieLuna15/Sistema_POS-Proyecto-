<?php

namespace App\Http\Controllers;

use App\Client;
use App\Product;
use App\Sale;
use Illuminate\Http\Request;

use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Sale\UpdateRequest;
use App\SaleDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;

/*
para libreria
*/
use App\NumerosEnLetras;

//Para sweet alert en Ventas
use RealRashid\SweetAlert\Facades\Alert;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sales = Sale::orderBy('sale_date','DESC')->get();
        //dd($sales);
        return view('admin.sale.index', compact('sales'));
    }

    public function create()
    {
        $clients = Client::get();
        $products=Product::where('status','ACTIVO')
                ->where('stock',">",0)->get();

        return view('admin.sale.create', compact('clients','products'));
    }

    public function store(StoreRequest $request)
    {
        $sale=Sale::create($request->all()+[
            'user_id'=>Auth::user()->id,
            'sale_date'=>Carbon::now('America/La_Paz'),
        ]);

        foreach($request->product_id as $key=>$product){
            $results[]= array(
                "product_id"=>$request->product_id[$key],
                "quantity"=>$request->quantity[$key],
                "price"=>$request->price[$key],
                "discount"=>$request->discount[$key],
            );
        }
        $sale->saleDetails()->createMany($results);
        Alert::toast('Venta registrada con éxito.', 'success');
        return redirect()->route('sales.index');
    }

    public function show(Sale $sale)
    {
        $descuentototal=0;
        $subtotal=0;
        $SaleDetails=$sale->SaleDetails;
        foreach ($SaleDetails as $SaleDetail) {
            $subtotal += $SaleDetail->quantity*$SaleDetail->price-$SaleDetail->quantity* $SaleDetail->price*$SaleDetail->discount/100;
            $descuentototal += $SaleDetail->quantity* $SaleDetail->price*$SaleDetail->discount/100;
        }
        return view('admin.sale.show', compact('sale','SaleDetails','subtotal','descuentototal'));
    }

    public function edit(Sale $sale)
    {
        $clients = Provider::get();
        return view('admin.sale.edit', compact('sale'));
    }

    public function update(UpdateRequest $request, Sale $sale)
    {
        //$sale->update($request->all());
        //return redirect()->route('sales.index');
    }

    public function destroy(Sale $sale)
    {
        //$sale->delete();
        //return redirect()->route('sales.index');
    }
    public function change_status(Sale $sale)
    {
        if($sale->status == 'CONFIRMADO'){
            $sale->update([ 'status' =>'CANCELADO']);
            return redirect()->back();
        }else{
            $sale->update([ 'status' =>'CONFIRMADO']);
            return redirect()->back();
        }
    }

    public function pdf(Sale $sale)
    {
        //dd($sale);
        $descuentototal=0;
        $subtotal=0;
        //Para mostrar el total en letras
        $totalLiteral=NumerosEnLetras::convertir($sale->total,'Bolivianos',true);
        //dd($totalLiteral);
        //
        $SaleDetails=$sale->SaleDetails;
        foreach ($SaleDetails as $SaleDetail) {
            $subtotal += $SaleDetail->quantity*$SaleDetail->price-$SaleDetail->quantity* $SaleDetail->price*$SaleDetail->discount/100;
            $descuentototal += $SaleDetail->quantity* $SaleDetail->price*$SaleDetail->discount/100;
        }
        //return Pdf::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
        $pdf = PDF::loadView('admin.sale.pdf', compact('sale','subtotal','descuentototal','SaleDetails','totalLiteral'));
        //return $pdf->download('Reporte-Nota_de_Compra_'.$sale->id.'.pdf');
        return $pdf->download('Nota_de_Venta_N°_'.$sale->id.'_Fec_'.$sale->sale_date.'.pdf');
    }
}
