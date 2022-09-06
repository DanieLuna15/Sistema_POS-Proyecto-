<?php

namespace App\Http\Controllers;


use App\Provider;
use App\Product;
use App\Purchase;
use Illuminate\Http\Request;

use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;
use App\PurchaseDetails;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $purchases = Purchase::get();
        return view('admin.purchase.index', compact('purchases'));
    }

    public function create()
    {
        $providers=Provider::get();
        $products=Product::get();
        return view('admin.purchase.create', compact('providers','products'));
    }

    public function store(StoreRequest $request)
    {
        //dd($request);
        $purchase=Purchase::create($request->all()+[
            'user_id'=>Auth::user()->id,
            'purchase_date'=>Carbon::now('America/La_Paz'),
        ]);

        foreach ($request->product_id as $key => $product){
            $results[]=array(
                "product_id"=>$request->product_id[$key],
                "quantity"=>$request->quantity[$key],
                "price"=>$request->price[$key]
            );
        }
        //dd($results);
        $purchase->purchaseDetails()->createMany($results);
        return redirect()->route('purchases.index');
    }

    public function show(Purchase $purchase)
    {

        $subtotal=0;
        $PurchaseDetails=$purchase->PurchaseDetails;
        foreach($PurchaseDetails as $PurchaseDetail){
            $subtotal+=$PurchaseDetail->quantity*$PurchaseDetail->price;
        }


        return view('admin.purchase.show', compact('purchase','PurchaseDetails','subtotal'));
    }

    public function edit(Purchase $purchase)
    {
        $providers = Provider::get();
        return view('admin.purchase.edit', compact('purchase'));
    }

    public function update(UpdateRequest $request, Purchase $purchase)
    {
        //$purchase->update($request->all());
        //return redirect()->route('purchases.index');
    }

    public function destroy(Purchase $purchase)
    {
        //$purchase->delete();
        //return redirect()->route('purchases.index');
    }
}
