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


class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sales = Sale::get();
        return view('admin.sale.index', compact('sales'));
    }

    public function create()
    {
        $clients = Client::get();
        $products=Product::where('status','ACTIVO')->get();
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
            $descuentototal += $SaleDetail->quantity* $SaleDetail->price*$SaleDetail->discount/100;
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
}
