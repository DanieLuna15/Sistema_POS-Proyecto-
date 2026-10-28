<?php

namespace App\Http\Controllers;

use App\Client;
use App\Product;
use App\Sale;
use App\Company;
use App\CashRegister;
use Illuminate\Http\Request;

use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Sale\UpdateRequest;
use App\SaleDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;
use App\NumerosEnLetras;
use RealRashid\SweetAlert\Facades\Alert;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:sales.create')->only(['create', 'store', 'pos_create', 'credit_create']);
        $this->middleware('can:sales.index')->only(['index', 'credits']);
        $this->middleware('can:sales.show')->only(['show', 'pdf', 'ticket']);
        $this->middleware('can:change.status.sales')->only(['change_status']);
    }

    public function index()
    {
        $sales = Sale::with(['client', 'user', 'branch'])->orderBy('sale_date', 'DESC')->get();
        return view('admin.sale.index', compact('sales'));
    }

    public function create()
    {
        Client::ensureDefaultForCompany();
        $clients = Client::orderBy('name', 'ASC')->get();
        $isRestaurant = optional(Company::current())->business_type === 'RESTAURANTE';

        $products = Product::where('status', 'ACTIVO')
            ->with(['branchStocks'])
            ->orderBy('name', 'ASC')
            ->get()
            ->filter(function ($p) use ($isRestaurant) {
                return $isRestaurant || $p->isService() || $p->stock > 0;
            })
            ->values();
        $paymentMethods = \App\PaymentMethod::active()->get();

        return view('admin.sale.create', compact('clients', 'products', 'paymentMethods'));
    }

    /**
     * Módulo 1: POS Táctil / Caja Rápida
     */
    public function pos_create()
    {
        Client::ensureDefaultForCompany();
        $clients = Client::orderBy('name', 'ASC')->get();
        $categories = \App\Category::orderBy('name', 'ASC')->get();
        $isRestaurant = optional(Company::current())->business_type === 'RESTAURANTE';

        $products = Product::where('status', 'ACTIVO')
            ->with(['category', 'brand', 'branchStocks'])
            ->orderBy('name', 'ASC')
            ->get()
            ->filter(function ($p) use ($isRestaurant) {
                return $isRestaurant || $p->isService() || $p->stock > 0;
            })
            ->values();
        $paymentMethods = \App\PaymentMethod::active()->get();

        return view('admin.sale.pos', compact('clients', 'categories', 'products', 'paymentMethods'));
    }

    /**
     * Módulo 2: Venta a Crédito / Con Cuotas
     */
    public function credit_create()
    {
        Client::ensureDefaultForCompany();
        $clients = Client::with(['sales' => function ($q) {
            $q->where('sale_type', 'CREDITO')->where('credit_status', 'PENDIENTE');
        }])->orderBy('name', 'ASC')->get();

        $isRestaurant = optional(Company::current())->business_type === 'RESTAURANTE';
        $products = Product::where('status', 'ACTIVO')
            ->with(['category', 'brand', 'branchStocks'])
            ->orderBy('name', 'ASC')
            ->get()
            ->filter(function ($p) use ($isRestaurant) {
                return $isRestaurant || $p->isService() || $p->stock > 0;
            })
            ->values();

        $paymentMethods = \App\PaymentMethod::active()->get();

        return view('admin.sale.credit_create', compact('clients', 'products', 'paymentMethods'));
    }

    /**
     * Listado de Cuentas por Cobrar (Ventas a Crédito)
     */
    public function credits()
    {
        $sales = Sale::where('sale_type', 'CREDITO')
            ->with(['client', 'user', 'installments', 'paymentMethod'])
            ->orderBy('sale_date', 'DESC')
            ->get();

        $totalCredito = $sales->sum('total');
        $totalPendiente = $sales->where('credit_status', 'PENDIENTE')->sum('balance_pending');
        $totalCobrado = $totalCredito - $totalPendiente;
        $paymentMethods = \App\PaymentMethod::active()->get();

        return view('admin.sale.credits', compact('sales', 'totalCredito', 'totalPendiente', 'totalCobrado', 'paymentMethods'));
    }

    /**
     * Registrar Pago / Abono de una Cuota
     */
    public function pay_installment(Request $request, \App\SaleInstallment $installment)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
        ]);

        $sale = $installment->sale;
        $paidNow = floatval($request->paid_amount);

        $installment->paid_amount = min($installment->amount, $installment->paid_amount + $paidNow);
        if ($installment->paid_amount >= $installment->amount) {
            $installment->status = 'PAGADO';
        }
        $installment->payment_date = Carbon::now('America/La_Paz');
        $installment->payment_method_id = $request->payment_method_id ?? null;
        $pm = $request->payment_method_id ? \App\PaymentMethod::find($request->payment_method_id) : null;
        $installment->payment_method = $pm ? $pm->name : ($request->payment_method ?? 'EFECTIVO');
        $installment->payment_reference = $request->payment_reference ?? null;
        $installment->notes = $request->notes;
        $installment->save();

        // Actualizar el saldo pendiente global de la venta a crédito
        $newBalance = max(0, $sale->balance_pending - $paidNow);
        $sale->balance_pending = $newBalance;
        if ($newBalance <= 0) {
            $sale->credit_status = 'PAGADO';
        }
        $sale->save();

        // Si hay caja abierta, recalcular totales
        $openRegister = CashRegister::activeOpen();
        if ($openRegister) {
            try {
                $openRegister->recalculateTotals();
            } catch (\Throwable $e) {}
        }

        Alert::toast('Pago de cuota #' . $installment->installment_number . ' registrado con éxito.', 'success');
        return redirect()->back();
    }

    public function store(StoreRequest $request, Sale $sale)
    {
        try {
            $createdSale = $sale->my_store($request);
            $msg = ($request->sale_type === 'CREDITO')
                ? 'Venta a Crédito registrada con éxito.'
                : 'Venta registrada con éxito.';
            Alert::toast($msg, 'success');

            if ($request->sale_type === 'CREDITO') {
                return redirect()->route('sales.credits');
            }
            if ($request->sale_type === 'POS_RAPIDO') {
                return redirect()->route('sales.ticket', $createdSale);
            }
            return redirect()->route('sales.index');
        } catch (\Exception $e) {
            Alert::error('Error al registrar la venta', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $descuentototal = 0;
        $subtotal = 0;
        $company = Company::current();
        $totalLiteral = NumerosEnLetras::convertir($sale->total, $company->currency_name ?? 'Bolivianos', true);
        $SaleDetails = $sale->saleDetails;
        foreach ($SaleDetails as $SaleDetail) {
            $subtotal += $SaleDetail->quantity * $SaleDetail->price;
            $descuentototal += $SaleDetail->quantity * $SaleDetail->price * $SaleDetail->discount / 100;
        }
        $installments = $sale->installments;
        return view('admin.sale.show', compact('sale', 'SaleDetails', 'subtotal', 'descuentototal', 'totalLiteral', 'installments'));
    }

    public function edit(Sale $sale)
    {
        $clients = Client::get();
        return view('admin.sale.edit', compact('sale'));
    }

    public function update(UpdateRequest $request, Sale $sale)
    {
    }

    public function destroy(Sale $sale)
    {
    }

    public function change_status(Sale $sale)
    {
        $refCode = 'VTA-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
        if ($sale->status == 'CONFIRMADO') {
            $sale->update(['status' => 'CANCELADO']);
            foreach ($sale->saleDetails as $detail) {
                if ($detail->product) {
                    $detail->product->add_stock($detail->quantity, 'ANULACION_VENTA', $refCode, 'Reingreso por anulación de venta');
                }
            }
            Alert::toast('Se canceló la venta y se restituyó el stock al Kardex.', 'success');
            return redirect()->back();
        } else {
            $sale->update(['status' => 'CONFIRMADO']);
            foreach ($sale->saleDetails as $detail) {
                if ($detail->product) {
                    $detail->product->substract_stock($detail->quantity, 'SALIDA_VENTA', $refCode, 'Reactivación de venta');
                }
            }
            Alert::toast('Se confirmó la venta.', 'success');
            return redirect()->back();
        }
    }

    public function pdf(Sale $sale)
    {
        $descuentototal = 0;
        $subtotal = 0;
        $company = Company::current();
        $totalLiteral = NumerosEnLetras::convertir($sale->total, $company->currency_name ?? 'Bolivianos', true);

        $SaleDetails = $sale->saleDetails;
        foreach ($SaleDetails as $SaleDetail) {
            $subtotal += $SaleDetail->quantity * $SaleDetail->price;
            $descuentototal += $SaleDetail->quantity * $SaleDetail->price * $SaleDetail->discount / 100;
        }

        $installments = $sale->installments;

        $pdf = PDF::loadView('admin.sale.pdf', compact('sale', 'subtotal', 'descuentototal', 'SaleDetails', 'totalLiteral', 'installments'));
        $pdf->setPaper('a4', 'portrait');

        if (request()->has('download')) {
            return $pdf->download('Nota_de_Venta_N_' . $sale->id . '.pdf');
        }

        return $pdf->stream('Nota_de_Venta_N_' . $sale->id . '.pdf');
    }

    public function ticket(Sale $sale)
    {
        $descuentototal = 0;
        $subtotal = 0;
        $company = Company::current();
        $totalLiteral = NumerosEnLetras::convertir($sale->total, $company->currency_name ?? 'Bolivianos', true);

        $SaleDetails = $sale->saleDetails;
        foreach ($SaleDetails as $SaleDetail) {
            $subtotal += $SaleDetail->quantity * $SaleDetail->price;
            $descuentototal += $SaleDetail->quantity * $SaleDetail->price * $SaleDetail->discount / 100;
        }

        $installments = $sale->installments;

        // Si se solicita descarga directa como archivo PDF de 80mm
        if (request()->has('pdf')) {
            $itemCount = $SaleDetails->count() + ($sale->sale_type === 'CREDITO' ? $installments->count() : 0);
            $heightPt = max(420, 260 + ($itemCount * 32));
            $customPaper = array(0, 0, 226.77, $heightPt);

            $pdf = PDF::loadView('admin.sale.ticket_80mm', compact('sale', 'subtotal', 'descuentototal', 'SaleDetails', 'totalLiteral', 'installments'))
                ->setPaper($customPaper);

            return $pdf->stream('Ticket_80mm_N_' . $sale->id . '.pdf');
        }

        return view('admin.sale.ticket_80mm', compact('sale', 'subtotal', 'descuentototal', 'SaleDetails', 'totalLiteral', 'installments'));
    }
}
