<?php

namespace App\Http\Controllers;

use App\RestaurantTable;
use App\RestaurantOrder;
use App\RestaurantOrderItem;
use App\Product;
use App\Category;
use App\Client;
use App\PaymentMethod;
use App\Sale;
use App\SaleDetails;
use App\Company;
use App\Branch;
use App\CashRegister;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class RestaurantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Salón de Mesas, Comandas Activas (Llevar/Delivery) y Monitor de Cocina (KDS)
     */
    public function index()
    {
        $company = Company::current();
        $branch = Branch::current();

        RestaurantTable::ensureDefaultTables($company->id, optional($branch)->id);

        $tables = RestaurantTable::with(['activeOrder.items.product', 'activeOrder.user'])
            ->when($branch, function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->orderBy('id', 'ASC')
            ->get();

        // Pedidos activos Para Llevar y Delivery
        $externalOrders = RestaurantOrder::with(['items.product', 'user', 'client'])
            ->whereIn('service_type', ['LLEVAR', 'DELIVERY'])
            ->whereIn('status', ['ABIERTA', 'EN_COCINA', 'LISTO', 'CUENTA_PEDIDA'])
            ->when($branch, function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->orderBy('id', 'DESC')
            ->get();

        // Comandas activas para el Monitor de Cocina (KDS)
        $kitchenOrders = RestaurantOrder::with(['table', 'items.product', 'user'])
            ->whereIn('status', ['ABIERTA', 'EN_COCINA', 'LISTO', 'CUENTA_PEDIDA'])
            ->when($branch, function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->orderBy('opened_at', 'ASC')
            ->get();

        return view('admin.restaurant.index', compact('tables', 'externalOrders', 'kitchenOrders', 'branch'));
    }

    /**
     * Crear una nueva Mesa o Ambiente en la sucursal actual
     */
    public function storeTable(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:60',
            'zone' => 'required|string|max:80',
            'capacity' => 'required|integer|min:1|max:50',
        ]);

        $branch = Branch::current();

        RestaurantTable::create([
            'company_id' => Company::currentId(),
            'branch_id' => optional($branch)->id,
            'name' => $request->name,
            'zone' => $request->zone,
            'capacity' => intval($request->capacity),
            'status' => 'LIBRE',
        ]);

        Alert::toast("Mesa '{$request->name}' agregada al salón.", 'success');
        return redirect()->route('restaurant.index');
    }

    /**
     * Abrir o continuar una Comanda en una Mesa específica
     */
    public function openTable(Request $request, RestaurantTable $table)
    {
        if ($table->active_order_id) {
            $order = RestaurantOrder::find($table->active_order_id);
            if ($order && !in_array($order->status, ['COBRADA', 'CANCELADA'])) {
                return redirect()->route('restaurant.orders.show', $order);
            }
        }

        $branch = Branch::current();
        $defaultClient = Client::orderBy('id', 'ASC')->first();

        $order = RestaurantOrder::create([
            'company_id' => Company::currentId(),
            'branch_id' => $table->branch_id ?: optional($branch)->id,
            'restaurant_table_id' => $table->id,
            'user_id' => Auth::id(),
            'client_id' => optional($defaultClient)->id,
            'service_type' => 'MESA',
            'diners_count' => intval($request->diners_count ?? $table->capacity ?? 2),
            'status' => 'ABIERTA',
            'opened_at' => Carbon::now('America/La_Paz'),
        ]);

        $order->update(['order_number' => 'CMD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT)]);
        $table->update([
            'status' => 'OCUPADA',
            'active_order_id' => $order->id,
        ]);

        return redirect()->route('restaurant.orders.show', $order);
    }

    /**
     * Crear Comanda Para Llevar o Delivery
     */
    public function storeExternalOrder(Request $request)
    {
        $request->validate([
            'service_type' => 'required|in:LLEVAR,DELIVERY',
            'customer_name' => 'required|string|max:120',
        ]);

        $branch = Branch::current();
        $defaultClient = Client::orderBy('id', 'ASC')->first();

        $order = RestaurantOrder::create([
            'company_id' => Company::currentId(),
            'branch_id' => optional($branch)->id,
            'restaurant_table_id' => null,
            'user_id' => Auth::id(),
            'client_id' => optional($defaultClient)->id,
            'service_type' => $request->service_type,
            'diners_count' => 1,
            'customer_name' => $request->customer_name,
            'delivery_phone' => $request->delivery_phone,
            'delivery_address' => $request->delivery_address,
            'status' => 'ABIERTA',
            'notes' => $request->notes,
            'opened_at' => Carbon::now('America/La_Paz'),
        ]);

        $order->update(['order_number' => 'CMD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT)]);

        return redirect()->route('restaurant.orders.show', $order);
    }

    /**
     * Pantalla Táctil de Toma de Comanda, Adición de Rondas, Pre-Cuenta y Cobro de Mesa
     */
    public function showOrder(RestaurantOrder $order)
    {
        $order->load(['table', 'items.product', 'user', 'client']);
        $categories = Category::orderBy('name', 'ASC')->get();
        $products = Product::where('status', 'ACTIVO')
            ->with(['category', 'brand', 'branchStocks'])
            ->orderBy('name', 'ASC')
            ->get()
            ->filter(function ($p) {
                return $p->isService() || $p->stock > 0;
            })
            ->values();

        $clients = Client::orderBy('name', 'ASC')->get();
        $paymentMethods = PaymentMethod::active()->get();

        return view('admin.restaurant.order', compact('order', 'categories', 'products', 'clients', 'paymentMethods'));
    }

    /**
     * Agregar un Plato / Bebida a la Comanda (Enviar a Cocina)
     */
    public function addItem(Request $request, RestaurantOrder $order)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty = floatval($request->quantity);

        if (!$product->isService() && $product->stock < $qty) {
            Alert::error('Stock insuficiente', "Solo quedan {$product->stock} unidades de '{$product->name}'.");
            return redirect()->back();
        }

        RestaurantOrderItem::create([
            'restaurant_order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $qty,
            'price' => floatval($request->price ?? $product->sell_price),
            'discount' => 0,
            'kitchen_status' => 'EN_COCINA',
            'notes' => $request->notes,
        ]);

        $order->status = 'EN_COCINA';
        $order->recalculate();

        if ($order->table) {
            $order->table->update(['status' => 'OCUPADA', 'active_order_id' => $order->id]);
        }

        Alert::toast("Agregado a comanda: {$qty}x {$product->name}", 'success');
        return redirect()->route('restaurant.orders.show', $order);
    }

    /**
     * Quitar un ítem de la comanda abierta
     */
    public function removeItem(RestaurantOrderItem $item)
    {
        $order = $item->order;
        $item->delete();
        if ($order) {
            $order->recalculate();
        }
        Alert::toast('Plato/bebida eliminado de la comanda.', 'info');
        return redirect()->back();
    }

    /**
     * Cambiar estado de preparación en cocina (EN_COCINA -> LISTO -> SERVIDO)
     */
    public function updateItemStatus(Request $request, RestaurantOrderItem $item)
    {
        $nextStatus = $request->kitchen_status ?? 'LISTO';
        $item->update(['kitchen_status' => $nextStatus]);

        $order = $item->order;
        if ($order) {
            $pendingCount = $order->items()->where('kitchen_status', 'EN_COCINA')->count();
            if ($pendingCount === 0 && $order->status === 'EN_COCINA') {
                $order->update(['status' => 'LISTO']);
            }
        }

        Alert::toast("Estado de plato actualizado a: {$nextStatus}", 'success');
        return redirect()->back();
    }

    /**
     * Imprimir Ticket Térmico 80mm de Comanda para Cocina / Barra (Sin precios, letra grande)
     */
    public function kitchenTicket(RestaurantOrder $order)
    {
        $order->load(['table', 'items.product', 'user']);
        return view('admin.restaurant.kitchen_ticket', compact('order'));
    }

    /**
     * Imprimir Ticket Térmico 80mm de Pre-Cuenta / Estado de Consumo para llevar a la mesa
     */
    public function prebillTicket(RestaurantOrder $order)
    {
        $order->load(['table', 'items.product', 'user']);
        $order->update(['status' => 'CUENTA_PEDIDA']);
        if ($order->table) {
            $order->table->update(['status' => 'CUENTA_PEDIDA']);
        }

        return view('admin.restaurant.prebill_ticket', compact('order'));
    }

    /**
     * Cobrar la Comanda, Generar Venta Oficial en Caja/Kardex y Liberar la Mesa
     */
    public function checkout(Request $request, RestaurantOrder $order)
    {
        $order->load(['table', 'items.product']);

        if ($order->items->isEmpty()) {
            Alert::warning('Comanda vacía', 'Agregue al menos un plato o bebida antes de cobrar la cuenta.');
            return redirect()->back();
        }

        try {
            $sale = DB::transaction(function () use ($request, $order) {
                $discountAmount = floatval($request->discount_amount ?? 0);
                $tipAmount = floatval($request->tip_amount ?? 0);

                $order->discount = $discountAmount;
                $order->tip_amount = $tipAmount;
                $order->recalculate();

                $paymentMethodId = $request->payment_method_id;
                $pmModel = $paymentMethodId ? PaymentMethod::find($paymentMethodId) : null;
                $paymentMethodName = $pmModel ? $pmModel->name : 'Efectivo (Bs.)';

                $cashReceived = $request->filled('cash_received') ? floatval($request->cash_received) : $order->total;
                $cashChange = max(0, $cashReceived - $order->total);

                $openRegister = CashRegister::activeOpen();
                $branch = Branch::current();

                // Descripción gastronómica para el ticket oficial de venta
                $serviceLabel = $order->service_type === 'MESA'
                    ? ('MESA: ' . ($order->table->name ?? '#' . $order->restaurant_table_id) . " ({$order->diners_count} pers.)")
                    : ($order->service_type . ': ' . ($order->customer_name ?? 'Mostrador') . ($order->delivery_address ? ' - Dir: ' . $order->delivery_address : ''));

                $sale = Sale::create([
                    'company_id' => Company::currentId(),
                    'branch_id' => $order->branch_id ?: optional($branch)->id,
                    'cash_register_id' => optional($openRegister)->id,
                    'client_id' => $request->client_id ?: ($order->client_id ?: Client::value('id')),
                    'user_id' => Auth::id(),
                    'sale_date' => Carbon::now('America/La_Paz'),
                    'tax' => 0,
                    'total' => $order->total,
                    'status' => 'CONFIRMADO',
                    'sale_type' => 'POS_RAPIDO',
                    'payment_method' => $paymentMethodName,
                    'payment_method_id' => $paymentMethodId,
                    'payment_condition' => 'CONTADO',
                    'payment_reference' => $request->payment_reference,
                    'cash_received' => $cashReceived,
                    'cash_change' => $cashChange,
                    'notes' => "{$order->order_number} | {$serviceLabel}" . ($tipAmount > 0 ? " | Propina/Serv: +{$tipAmount}" : ""),
                ]);

                $refCode = 'VTA-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT);

                foreach ($order->items as $item) {
                    $product = $item->product;
                    if ($product && !$product->isService()) {
                        $product->substract_stock(
                            $item->quantity,
                            'SALIDA_VENTA',
                            $refCode,
                            "Venta en restaurante ({$order->order_number})",
                            $sale->branch_id
                        );
                    }

                    SaleDetails::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'discount' => $item->discount,
                    ]);
                }

                // Cerrar la comanda y liberar la mesa
                $order->update([
                    'sale_id' => $sale->id,
                    'client_id' => $sale->client_id,
                    'status' => 'COBRADA',
                    'closed_at' => Carbon::now('America/La_Paz'),
                ]);

                if ($order->table) {
                    $order->table->update([
                        'status' => 'LIBRE',
                        'active_order_id' => null,
                    ]);
                }

                if ($openRegister) {
                    try {
                        $openRegister->recalculateTotals();
                    } catch (\Throwable $e) {}
                }

                return $sale;
            });

            Alert::toast("Cuenta {$order->order_number} cobrada con éxito. Mesa liberada.", 'success');
            return redirect()->route('sales.ticket', ['sale' => $sale->id]);
        } catch (\Exception $e) {
            Alert::error('Error al cobrar la cuenta', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Cancelar / Liberar una mesa o comanda sin cobro
     */
    public function cancelOrder(RestaurantOrder $order)
    {
        if ($order->table) {
            $order->table->update([
                'status' => 'LIBRE',
                'active_order_id' => null,
            ]);
        }

        $order->update([
            'status' => 'CANCELADA',
            'closed_at' => Carbon::now('America/La_Paz'),
        ]);

        Alert::toast("Comanda {$order->order_number} cancelada y mesa liberada.", 'info');
        return redirect()->route('restaurant.index');
    }
}
