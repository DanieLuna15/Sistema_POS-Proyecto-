<?php

namespace App\Http\Controllers;

use App\StockTransfer;
use App\StockTransferDetail;
use App\Branch;
use App\Product;
use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;

class StockTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listado de Traspasos entre Sucursales y Formulario de Nuevo Traspaso
     */
    public function index()
    {
        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'user', 'details.product'])
            ->orderBy('transfer_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        $branches = Branch::where('status', 'ACTIVO')->orderBy('is_main', 'DESC')->orderBy('id', 'ASC')->get();
        $products = Product::where('status', 'ACTIVO')
            ->where('item_type', '!=', 'SERVICIO')
            ->with(['branchStocks.branch', 'brand'])
            ->orderBy('name', 'ASC')
            ->get();

        // Mapa de stock por sucursal para validación dinámica en JavaScript
        $branchStockMap = [];
        foreach ($products as $p) {
            $branchStockMap[$p->id] = [];
            foreach ($branches as $b) {
                $branchStockMap[$p->id][$b->id] = $p->stockInBranch($b->id);
            }
        }

        return view('admin.transfer.index', compact('transfers', 'branches', 'products', 'branchStockMap'));
    }

    /**
     * Ejecutar Traspaso de Mercadería de Sucursal Origen -> Sucursal Destino
     */
    public function store(Request $request)
    {
        $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'product_id' => 'required|array|min:1',
            'quantity' => 'required|array|min:1',
        ], [
            'to_branch_id.different' => 'La sucursal de destino debe ser diferente a la sucursal de origen.',
            'product_id.required' => 'Debe agregar al menos un producto para traspasar.',
        ]);

        $fromBranch = Branch::findOrFail($request->from_branch_id);
        $toBranch = Branch::findOrFail($request->to_branch_id);

        try {
            $transfer = DB::transaction(function () use ($request, $fromBranch, $toBranch) {
                // 1. Verificar stock disponible en la Sucursal de Origen
                foreach ($request->product_id as $idx => $prodId) {
                    $qty = floatval($request->quantity[$idx] ?? 0);
                    if ($qty <= 0) {
                        throw new \Exception("La cantidad a traspasar debe ser mayor a cero.");
                    }
                    $product = Product::with('branchStocks')->findOrFail($prodId);
                    $availableInOrigin = $product->stockInBranch($fromBranch->id);
                    if ($availableInOrigin < $qty) {
                        throw new \Exception("Stock insuficiente de '{$product->name}' en {$fromBranch->name}. Disponible: {$availableInOrigin}, Solicitado: {$qty}.");
                    }
                }

                // 2. Crear cabecera del traspaso
                $transfer = StockTransfer::create([
                    'company_id' => Company::currentId(),
                    'from_branch_id' => $fromBranch->id,
                    'to_branch_id' => $toBranch->id,
                    'user_id' => Auth::id(),
                    'transfer_date' => Carbon::now('America/La_Paz'),
                    'status' => 'COMPLETADO',
                    'notes' => $request->notes,
                ]);

                $refCode = 'TRP-' . str_pad($transfer->id, 6, '0', STR_PAD_LEFT);
                $transfer->update(['reference_code' => $refCode]);

                // 3. Mover el stock de Origen a Destino y registrar ambos movimientos en Kardex
                foreach ($request->product_id as $idx => $prodId) {
                    $qty = floatval($request->quantity[$idx]);
                    $product = Product::findOrFail($prodId);

                    // Salida en Sucursal Origen
                    $product->substract_stock(
                        $qty,
                        'TRASPASO_SALIDA',
                        $refCode,
                        "Traspaso enviado hacia: {$toBranch->name}" . ($request->notes ? " ({$request->notes})" : ""),
                        $fromBranch->id
                    );

                    // Entrada en Sucursal Destino
                    $product->add_stock(
                        $qty,
                        'TRASPASO_ENTRADA',
                        $refCode,
                        "Traspaso recibido desde: {$fromBranch->name}" . ($request->notes ? " ({$request->notes})" : ""),
                        $product->purchase_price,
                        $toBranch->id
                    );

                    StockTransferDetail::create([
                        'stock_transfer_id' => $transfer->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_cost' => $product->purchase_price ?? 0,
                    ]);
                }

                return $transfer;
            });

            Alert::success(
                '¡Traspaso Ejecutado!',
                "Guía {$transfer->reference_code}: Mercadería transferida de '{$fromBranch->name}' a '{$toBranch->name}' y registrada en Kardex."
            );

            return redirect()->route('transfers.index');
        } catch (\Exception $e) {
            Alert::error('No se pudo realizar el traspaso', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Generar Guía de Remisión / Comprobante de Traspaso en PDF
     */
    public function pdf(StockTransfer $transfer)
    {
        $transfer->load(['fromBranch', 'toBranch', 'user', 'details.product']);
        $company = Company::current();

        $pdf = Pdf::loadView('admin.transfer.pdf', compact('transfer', 'company'));
        return $pdf->stream("Guia_Traspaso_{$transfer->reference_code}.pdf");
    }

    /**
     * Anular un Traspaso (Revierte el stock a la sucursal de origen)
     */
    public function cancel(StockTransfer $transfer)
    {
        if ($transfer->status === 'ANULADO') {
            Alert::warning('Ya anulado', 'Este traspaso ya fue anulado anteriormente.');
            return redirect()->back();
        }

        $transfer->load(['fromBranch', 'toBranch', 'details.product']);

        try {
            DB::transaction(function () use ($transfer) {
                $refCode = 'ANU-' . $transfer->reference_code;

                foreach ($transfer->details as $detail) {
                    $product = $detail->product;
                    if ($product) {
                        // Descontar de la sucursal destino
                        $product->substract_stock(
                            $detail->quantity,
                            'AJUSTE_SALIDA',
                            $refCode,
                            "Reversión por anulación de traspaso {$transfer->reference_code}",
                            $transfer->to_branch_id
                        );

                        // Devolver a la sucursal origen
                        $product->add_stock(
                            $detail->quantity,
                            'AJUSTE_ENTRADA',
                            $refCode,
                            "Retorno de stock por anulación de traspaso {$transfer->reference_code}",
                            $detail->unit_cost,
                            $transfer->from_branch_id
                        );
                    }
                }

                $transfer->update(['status' => 'ANULADO']);
            });

            Alert::toast("Traspaso {$transfer->reference_code} anulado y stock retornado a {$transfer->fromBranch->name}.", 'info');
        } catch (\Exception $e) {
            Alert::error('Error al anular', $e->getMessage());
        }

        return redirect()->route('transfers.index');
    }
}

