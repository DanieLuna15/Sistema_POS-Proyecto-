<?php

namespace App\Http\Controllers;

use App\InventoryMovement;
use App\Product;
use App\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class InventoryMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Historial de Kardex y Ajustes de Inventario
     */
    public function index(Request $request)
    {
        $products = Product::with('branchStocks.branch')->orderBy('name', 'ASC')->get();
        $branches = Branch::orderBy('is_main', 'DESC')->get();
        $activeBranch = Branch::current();

        $query = InventoryMovement::with(['product', 'user', 'branch'])->orderBy('movement_date', 'DESC')->orderBy('id', 'DESC');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }
        if ($request->filled('branch_id')) {
            if ($request->branch_id !== 'ALL') {
                $query->where('branch_id', $request->branch_id);
            }
        } elseif ($activeBranch) {
            $query->where(function ($q) use ($activeBranch) {
                $q->where('branch_id', $activeBranch->id)->orWhereNull('branch_id');
            });
        }

        $movements = $query->take(200)->get();

        // Alertas rápidas: Bajo stock en la Sucursal Activa y Próximos a vencer
        $lowStockProducts = $products->filter(function ($p) {
            return !$p->isService() && $p->stock <= ($p->stock_min ?? 5);
        })->sortBy('stock');

        $expiringProducts = Product::with('branchStocks')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', Carbon::now('America/La_Paz')->addDays(45))
            ->orderBy('expiration_date', 'ASC')
            ->get();

        return view('admin.kardex.index', compact('movements', 'products', 'branches', 'lowStockProducts', 'expiringProducts'));
    }

    /**
     * Registrar un Ajuste Manual de Inventario (Entrada por conteo o Salida por merma/daño/vencimiento)
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:AJUSTE_ENTRADA,AJUSTE_SALIDA,MERMA_VENCIMIENTO,INVENTARIO_INICIAL',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'required|string|max:400',
        ]);

        $product = Product::findOrFail($request->product_id);
        if ($product->isService()) {
            Alert::warning('Ítem de tipo Servicio', 'Los servicios no manejan stock físico en almacén.');
            return redirect()->back();
        }

        $qty = floatval($request->quantity);
        $type = $request->movement_type;
        $ref = 'AJU-' . date('YmdHis');

        if (in_array($type, ['AJUSTE_ENTRADA', 'INVENTARIO_INICIAL'])) {
            $product->add_stock($qty, $type, $ref, $request->notes, $request->unit_cost ?? $product->purchase_price);
            Alert::toast("Ingreso de ajuste (+{$qty}) aplicado a '{$product->name}'.", 'success');
        } else {
            if ($product->stock < $qty) {
                Alert::error('Stock insuficiente', "No puede descontar {$qty} unidades porque el stock actual es {$product->stock}.");
                return redirect()->back();
            }
            $product->substract_stock($qty, $type, $ref, $request->notes);
            Alert::toast("Salida por ajuste/merma (-{$qty}) aplicada a '{$product->name}'.", 'success');
        }

        return redirect()->route('kardex.index');
    }
}

