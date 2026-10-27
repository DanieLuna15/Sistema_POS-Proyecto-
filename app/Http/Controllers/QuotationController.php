<?php

namespace App\Http\Controllers;

use App\Quotation;
use App\QuotationDetail;
use App\Client;
use App\Product;
use App\Sale;
use App\Branch;
use App\Company;
use App\PaymentMethod;
use App\NumerosEnLetras;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $quotations = Quotation::with(['client', 'user', 'branch', 'convertedSale'])
            ->orderBy('quotation_date', 'DESC')
            ->get();

        return view('admin.quotation.index', compact('quotations'));
    }

    public function create()
    {
        $clients = Client::orderBy('name', 'ASC')->get();
        $products = Product::where('status', 'ACTIVO')
            ->with(['category', 'brand'])
            ->orderBy('name', 'ASC')
            ->get();
        $company = Company::current();

        return view('admin.quotation.create', compact('clients', 'products', 'company'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|array|min:1',
            'quantity' => 'required|array|min:1',
            'price' => 'required|array|min:1',
        ]);

        try {
            $quotation = DB::transaction(function () use ($request) {
                $subtotal = 0;
                $discountTotal = 0;
                $detailsData = [];

                foreach ($request->product_id as $key => $productId) {
                    $qty = floatval($request->quantity[$key]);
                    $price = floatval($request->price[$key]);
                    $discountPct = isset($request->discount[$key]) ? floatval($request->discount[$key]) : 0;

                    $lineGross = $qty * $price;
                    $lineDisc = ($lineGross * $discountPct) / 100;

                    $subtotal += $lineGross;
                    $discountTotal += $lineDisc;

                    $detailsData[] = [
                        'product_id' => $productId,
                        'quantity' => $qty,
                        'price' => $price,
                        'discount' => $discountPct,
                    ];
                }

                $netSubtotal = max(0, $subtotal - $discountTotal);
                $taxPct = floatval($request->tax ?? 0);
                $taxAmount = ($netSubtotal * $taxPct) / 100;
                $total = round($netSubtotal + $taxAmount, 2);

                $branch = Branch::current();
                $validDays = intval($request->valid_days ?? 15);

                $quotation = Quotation::create([
                    'branch_id' => $branch ? $branch->id : null,
                    'client_id' => $request->client_id,
                    'user_id' => Auth::id(),
                    'quotation_date' => Carbon::now('America/La_Paz'),
                    'valid_until' => Carbon::now('America/La_Paz')->addDays($validDays)->toDateString(),
                    'tax' => $taxPct,
                    'subtotal' => round($subtotal, 2),
                    'discount_total' => round($discountTotal, 2),
                    'total' => $total,
                    'status' => 'PENDIENTE',
                    'notes' => $request->notes,
                    'terms' => $request->terms,
                ]);

                $quotation->details()->createMany($detailsData);

                return $quotation;
            });

            Alert::toast('Cotización / Proforma N° ' . str_pad($quotation->id, 5, '0', STR_PAD_LEFT) . ' creada con éxito.', 'success');
            return redirect()->route('quotations.show', $quotation);
        } catch (\Exception $e) {
            Alert::error('Error al crear la cotización', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['client', 'user', 'branch', 'details.product', 'convertedSale']);
        $company = Company::current();
        $totalLiteral = NumerosEnLetras::convertir($quotation->total, $company->currency_name ?? 'Bolivianos', true);
        $paymentMethods = PaymentMethod::active()->get();

        return view('admin.quotation.show', compact('quotation', 'totalLiteral', 'paymentMethods'));
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['client', 'user', 'branch', 'details.product']);
        $company = Company::current();
        $totalLiteral = NumerosEnLetras::convertir($quotation->total, $company->currency_name ?? 'Bolivianos', true);

        $pdf = PDF::loadView('admin.quotation.pdf', compact('quotation', 'totalLiteral'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Cotizacion_Proforma_N_' . str_pad($quotation->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    /**
     * Convertir una Cotización directamente en una Venta Confirmada con 1 clic
     */
    public function convertToSale(Request $request, Quotation $quotation)
    {
        if ($quotation->status === 'CONVERTIDA' && $quotation->converted_sale_id) {
            Alert::info('Cotización ya convertida', 'Esta cotización ya fue transformada en una venta.');
            return redirect()->route('sales.show', $quotation->converted_sale_id);
        }

        $quotation->load('details.product');

        try {
            $saleRequestData = [
                'client_id' => $quotation->client_id,
                'sale_type' => $request->sale_type ?? 'REGULAR',
                'payment_condition' => 'CONTADO',
                'payment_method_id' => $request->payment_method_id ?? null,
                'payment_method' => $request->payment_method ?? 'Efectivo (Bs.)',
                'payment_reference' => $request->payment_reference ?? ('COT-' . str_pad($quotation->id, 5, '0', STR_PAD_LEFT)),
                'notes' => 'Generada a partir de Cotización #' . str_pad($quotation->id, 5, '0', STR_PAD_LEFT),
                'product_id' => [],
                'quantity' => [],
                'price' => [],
                'discount' => [],
            ];

            foreach ($quotation->details as $detail) {
                $saleRequestData['product_id'][] = $detail->product_id;
                $saleRequestData['quantity'][] = $detail->quantity;
                $saleRequestData['price'][] = $detail->price;
                $saleRequestData['discount'][] = $detail->discount;
            }

            $saleModel = new Sale();
            $createdSale = $saleModel->my_store(new Request($saleRequestData));

            $quotation->update([
                'status' => 'CONVERTIDA',
                'converted_sale_id' => $createdSale->id,
            ]);

            Alert::toast('¡Cotización convertida en Venta N° ' . str_pad($createdSale->id, 6, '0', STR_PAD_LEFT) . ' con éxito!', 'success');
            return redirect()->route('sales.show', $createdSale);
        } catch (\Exception $e) {
            Alert::error('No se pudo convertir en venta', $e->getMessage());
            return redirect()->back();
        }
    }
}

