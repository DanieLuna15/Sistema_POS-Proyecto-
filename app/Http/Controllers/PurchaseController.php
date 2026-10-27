<?php

namespace App\Http\Controllers;

use App\Provider;
use App\Product;
use App\Purchase;
use App\Company;
use Illuminate\Http\Request;

use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;
use App\PurchaseDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;
use App\NumerosEnLetras;
use RealRashid\SweetAlert\Facades\Alert;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:purchases.create')->only(['create', 'store']);
        $this->middleware('can:purchases.index')->only(['index']);
        $this->middleware('can:purchases.show')->only(['show', 'pdf']);
        $this->middleware('can:change.status.purchases')->only(['change_status']);
    }

    public function index()
    {
        $purchases = Purchase::with(['provider', 'user', 'branch'])->orderBy('purchase_date', 'DESC')->get();
        return view('admin.purchase.index', compact('purchases'));
    }

    public function create()
    {
        $providers = Provider::orderBy('name', 'ASC')->get();
        $products = Product::where('status', 'ACTIVO')->where('item_type', '!=', 'SERVICIO')->orderBy('name', 'ASC')->get();
        return view('admin.purchase.create', compact('providers', 'products'));
    }

    public function store(StoreRequest $request, Purchase $purchase)
    {
        try {
            $purchase->my_store($request);
            Alert::toast('Compra registrada con éxito y Kardex actualizado.', 'success');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            Alert::error('Error al registrar la compra', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $subtotal = 0;
        $company = Company::current();
        $totalLiteral = NumerosEnLetras::convertir($purchase->total, $company->currency_name ?? 'Bolivianos', true);
        $PurchaseDetails = $purchase->PurchaseDetails;
        foreach ($PurchaseDetails as $PurchaseDetail) {
            $subtotal += $PurchaseDetail->quantity * $PurchaseDetail->price;
        }
        return view('admin.purchase.show', compact('purchase', 'PurchaseDetails', 'subtotal', 'totalLiteral'));
    }

    public function edit(Purchase $purchase)
    {
    }

    public function update(UpdateRequest $request, Purchase $purchase)
    {
    }

    public function destroy(Purchase $purchase)
    {
    }

    public function change_status(Purchase $purchase)
    {
        $refCode = 'CMP-' . str_pad($purchase->id, 6, '0', STR_PAD_LEFT);
        if ($purchase->status == 'CONFIRMADO') {
            $purchase->update(['status' => 'CANCELADO']);
            foreach ($purchase->PurchaseDetails as $detail) {
                if ($detail->product) {
                    $detail->product->substract_stock($detail->quantity, 'ANULACION_COMPRA', $refCode, 'Salida por anulación de compra');
                }
            }
            Alert::toast('Se canceló la compra y se ajustó el Kardex.', 'success');
            return redirect()->back();
        } else {
            $purchase->update(['status' => 'CONFIRMADO']);
            foreach ($purchase->PurchaseDetails as $detail) {
                if ($detail->product) {
                    $detail->product->add_stock($detail->quantity, 'ENTRADA_COMPRA', $refCode, 'Reactivación de compra', $detail->price);
                }
            }
            Alert::toast('Se confirmó la compra.', 'success');
            return redirect()->back();
        }
    }

    public function pdf(Purchase $purchase)
    {
        $subtotal = 0;
        $company = Company::current();
        $totalLiteral = NumerosEnLetras::convertir($purchase->total, $company->currency_name ?? 'Bolivianos', true);
        $PurchaseDetails = $purchase->PurchaseDetails;
        foreach ($PurchaseDetails as $PurchaseDetail) {
            $subtotal += $PurchaseDetail->quantity * $PurchaseDetail->price;
        }

        $pdf = PDF::loadView('admin.purchase.pdf', compact('purchase', 'subtotal', 'PurchaseDetails', 'totalLiteral'));
        $pdf->setPaper('a4', 'portrait');

        if (request()->has('download')) {
            return $pdf->download('Nota_de_Compra_N_' . $purchase->id . '.pdf');
        }

        return $pdf->stream('Nota_de_Compra_N_' . $purchase->id . '.pdf');
    }
}
