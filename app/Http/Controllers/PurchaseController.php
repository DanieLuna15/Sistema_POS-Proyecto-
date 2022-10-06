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

use Barryvdh\DomPDF\Facade\Pdf;

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
        $products=Product::where('status','ACTIVO')->get();
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
        //$providers = Provider::get();
        //return view('admin.purchase.edit', compact('purchase'));
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
    public function change_status(Purchase $purchase)
    {
        if($purchase->status == 'CONFIRMADO'){
            $purchase->update([ 'status' =>'CANCELADO']);
            return redirect()->back();
        }else{
            $purchase->update([ 'status' =>'CONFIRMADO']);
            return redirect()->back();
        }
    }

    public function pdf(Purchase $purchase)
    {
        //dd($purchase);
        $subtotal=0;
        $PurchaseDetails=$purchase->PurchaseDetails;
        foreach($PurchaseDetails as $PurchaseDetail){
            $subtotal+=$PurchaseDetail->quantity*$PurchaseDetail->price;
        }
        //return Pdf::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
        $pdf = PDF::loadView('admin.purchase.pdf', compact('purchase','subtotal','PurchaseDetails'));
        //return $pdf->download('Reporte-Nota_de_Compra_'.$purchase->id.'.pdf');
        return $pdf->download('Reporte-Nota_de_Compra_'.$purchase->id.'_Fec_'.$purchase->purchase_date.'.pdf');
    }
}
