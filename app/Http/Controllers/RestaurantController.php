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
        $zoneName = trim($request->filled('custom_zone') ? $request->custom_zone : ($request->zone ?: 'Salón Principal'));

        $request->validate([
            'name' => 'required|string|max:60',
            'capacity' => 'required|integer|min:1|max:50',
        ]);

        $branch = Branch::current();
        $existingInZone = RestaurantTable::where('zone', $zoneName)
            ->when($branch, fn($q) => $q->where('branch_id', $branch->id))
            ->count();

        $col = $existingInZone % 4;
        $row = intdiv($existingInZone, 4);

        RestaurantTable::create([
            'company_id' => Company::currentId(),
            'branch_id' => optional($branch)->id,
            'name' => $request->name,
            'zone' => $zoneName,
            'capacity' => intval($request->capacity),
            'shape' => $request->shape ?: 'CUADRADA',
            'pos_x' => intval($request->pos_x ?? (40 + ($col * 190))),
            'pos_y' => intval($request->pos_y ?? (40 + ($row * 160))),
            'status' => 'LIBRE',
        ]);

        Alert::toast("Mesa '{$request->name}' agregada al área '{$zoneName}'.", 'success');
        return redirect()->route('restaurant.index', ['zone' => $zoneName]);
    }

    /**
     * Guardar posición X/Y y forma en el Mapa / Plano Visual 2D (Drag & Drop)
     */
    public function updateTablePosition(Request $request, RestaurantTable $table)
    {
        $data = [
            'pos_x' => max(10, intval($request->pos_x ?? $table->pos_x)),
            'pos_y' => max(10, intval($request->pos_y ?? $table->pos_y)),
        ];
        if ($request->filled('shape')) {
            $data['shape'] = $request->shape;
        }
        $table->update($data);

        return response()->json(['ok' => true, 'table' => $table]);
    }

    /**
     * Editar datos de una Mesa (Nombre, Zona/Área, Capacidad, Forma)
     */
    public function updateTable(Request $request, RestaurantTable $table)
    {
        $zoneName = trim($request->filled('custom_zone') ? $request->custom_zone : ($request->zone ?: $table->zone));

        $table->update([
            'name' => $request->name ?: $table->name,
            'zone' => $zoneName,
            'capacity' => intval($request->capacity ?: $table->capacity),
            'shape' => $request->shape ?: $table->shape,
        ]);

        Alert::toast("Mesa '{$table->name}' actualizada correctamente.", 'success');
        return redirect()->route('restaurant.index');
    }

    /**
     * Eliminar una Mesa libre del salón
     */
    public function destroyTable(RestaurantTable $table)
    {
        if ($table->status !== 'LIBRE' && $table->active_order_id) {
            Alert::warning('Mesa ocupada', 'No puede eliminar una mesa con cuenta activa.');
            return redirect()->back();
        }
        $name = $table->name;
        $table->delete();
        Alert::toast("Mesa '{$name}' eliminada del salón.", 'info');
        return redirect()->route('restaurant.index');
    }

    /**
     * Módulo Exclusivo para Personal de Cocina / Barra (Pantalla KDS Dedicada)
     */
    public function kitchenIndex()
    {
        $branch = Branch::current();

        $kitchenOrders = RestaurantOrder::with(['table', 'items.product.category', 'user'])
            ->whereIn('status', ['ABIERTA', 'EN_COCINA', 'LISTO', 'CUENTA_PEDIDA'])
            ->whereHas('items')
            ->when($branch, function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->orderBy('opened_at', 'ASC')
            ->get();

        return view('admin.restaurant.kitchen', compact('kitchenOrders', 'branch'));
    }

    /**
     * Marcar todos los platos de una comanda como LISTO o SERVIDO desde Cocina (Soporta AJAX sin recargar)
     */
    public function markOrderReady(Request $request, RestaurantOrder $order)
    {
        $targetStatus = $request->target_status ?: 'LISTO';
        $order->items()->where('kitchen_status', '!=', 'SERVIDO')->update([
            'kitchen_status' => $targetStatus === 'SERVIDO' ? 'SERVIDO' : 'LISTO',
        ]);
        $order->update(['status' => 'LISTO']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'order_id' => $order->id,
                'target_status' => $targetStatus,
                'message' => "Comanda {$order->order_number} marcada como {$targetStatus}.",
            ]);
        }

        Alert::toast("Comanda {$order->order_number} marcada como {$targetStatus}.", 'success');
        return redirect()->back();
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
        $defaultClient = Client::ensureDefaultForCompany();

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
        $defaultClient = Client::ensureDefaultForCompany();

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
        Client::ensureDefaultForCompany();
        $order->load(['table', 'items.product', 'user', 'client']);
        $categories = Category::orderBy('name', 'ASC')->get();
        $products = Product::where('status', 'ACTIVO')
            ->with(['category', 'brand', 'branchStocks', 'comboItems.componentProduct'])
            ->orderBy('name', 'ASC')
            ->get();

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
        $notes = trim($request->notes ?? '');
        $isRestaurant = optional(Company::current())->business_type === 'RESTAURANTE';

        if (!$product->isService() && !$isRestaurant && $product->stock < $qty) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => "Solo quedan {$product->stock} unidades de '{$product->name}'."], 422);
            }
            Alert::error('Stock insuficiente', "Solo quedan {$product->stock} unidades de '{$product->name}'.");
            return redirect()->back();
        }

        // Si ya existe el mismo plato en estado EN_COCINA con la misma nota, sumar la cantidad
        $existingItem = RestaurantOrderItem::where('restaurant_order_id', $order->id)
            ->where('product_id', $product->id)
            ->where('kitchen_status', 'EN_COCINA')
            ->where(function ($q) use ($notes) {
                if ($notes === '') {
                    $q->whereNull('notes')->orWhere('notes', '');
                } else {
                    $q->where('notes', $notes);
                }
            })
            ->first();

        $wasExisting = false;
        if ($existingItem) {
            $existingItem->quantity = floatval($existingItem->quantity) + $qty;
            $existingItem->save();
            $item = $existingItem;
            $wasExisting = true;
        } else {
            $item = RestaurantOrderItem::create([
                'restaurant_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'price' => floatval($request->price ?? $product->sell_price),
                'discount' => 0,
                'kitchen_status' => 'EN_COCINA',
                'notes' => $notes !== '' ? $notes : null,
            ]);
        }

        $order->status = 'EN_COCINA';
        $order->recalculate();

        if ($order->table) {
            $order->table->update(['status' => 'OCUPADA', 'active_order_id' => $order->id]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'is_new' => !$wasExisting,
                'item_id' => $item->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => floatval($item->price),
                'quantity' => intval($item->quantity),
                'notes' => $item->notes,
                'kitchen_status' => $item->kitchen_status,
                'line_subtotal' => floatval($item->quantity) * floatval($item->price),
                'order_total' => floatval($order->total),
                'order_subtotal' => floatval($order->subtotal),
                'total_items' => intval($order->items()->sum('quantity')),
            ]);
        }

        Alert::toast("Agregado a comanda: {$qty}x {$product->name}", 'success');
        return redirect()->route('restaurant.orders.show', $order);
    }

    /**
     * Actualizar cantidad (+ / -) de un ítem ya comandado en la mesa
     */
    public function updateItemQty(Request $request, RestaurantOrderItem $item)
    {
        $order = $item->order;
        $newQty = intval($request->quantity ?? 1);

        if ($newQty <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $newQty]);
        }

        if ($order) {
            $order->recalculate();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'deleted' => $newQty <= 0,
                'quantity' => max(0, $newQty),
                'line_subtotal' => max(0, $newQty) * floatval($item->price),
                'order_subtotal' => $order ? $order->subtotal : 0,
                'order_total' => $order ? $order->total : 0,
                'total_items' => $order ? $order->items()->sum('quantity') : 0,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Quitar un ítem de la comanda abierta
     */
    public function removeItem(Request $request, RestaurantOrderItem $item)
    {
        $order = $item->order;
        $item->delete();
        if ($order) {
            $order->recalculate();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'order_subtotal' => $order ? $order->subtotal : 0,
                'order_total' => $order ? $order->total : 0,
                'total_items' => $order ? $order->items()->sum('quantity') : 0,
            ]);
        }

        Alert::toast('Plato/bebida eliminado de la comanda.', 'info');
        return redirect()->back();
    }

    /**
     * Cambiar estado de preparación en cocina (EN_COCINA -> LISTO -> SERVIDO) (Soporta AJAX sin recargar)
     */
    public function updateItemStatus(Request $request, RestaurantOrderItem $item)
    {
        $nextStatus = $request->kitchen_status ?? 'LISTO';
        $item->update(['kitchen_status' => $nextStatus]);

        $order = $item->order;
        $pendingCount = 0;
        if ($order) {
            $pendingCount = $order->items()->where('kitchen_status', 'EN_COCINA')->count();
            if ($pendingCount === 0 && $order->status === 'EN_COCINA') {
                $order->update(['status' => 'LISTO']);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'item_id' => $item->id,
                'order_id' => optional($order)->id,
                'kitchen_status' => $nextStatus,
                'order_pending_count' => $pendingCount,
                'order_status' => optional($order)->status,
            ]);
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
        if ($order->sale_id && Sale::where('id', $order->sale_id)->exists()) {
            if ($order->table) {
                $order->table->update(['status' => 'LIBRE', 'active_order_id' => null]);
            }
            Alert::toast("Cuenta {$order->order_number} cobrada con éxito. Mesa liberada.", 'success');
            return redirect()->route('sales.ticket', ['sale' => $order->sale_id]);
        }

        $order->load(['table', 'items.product']);

        if ($order->items->isEmpty()) {
            Alert::warning('Comanda vacía', 'Agregue al menos un plato o bebida antes de cobrar la cuenta.');
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

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
            $companyId = Company::currentId() ?: 1;

            $branchId = $order->branch_id 
                ?: optional($branch)->id 
                ?: optional(Branch::where('company_id', $companyId)->where('status', 'ACTIVO')->where('is_main', true)->first())->id
                ?: optional(Branch::where('company_id', $companyId)->where('status', 'ACTIVO')->first())->id
                ?: 1;

            // Descripción gastronómica para el ticket oficial de venta
            $serviceLabel = $order->service_type === 'MESA'
                ? ('MESA: ' . ($order->table->name ?? '#' . $order->restaurant_table_id) . " ({$order->diners_count} pers.)")
                : ($order->service_type . ': ' . ($order->customer_name ?? 'Mostrador') . ($order->delivery_address ? ' - Dir: ' . $order->delivery_address : ''));

            $defaultClient = Client::ensureDefaultForCompany($companyId);
            $clientId = $request->client_id ?: ($order->client_id ?: optional($defaultClient)->id ?: 1);

            $sale = Sale::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'cash_register_id' => optional($openRegister)->id,
                'client_id' => $clientId,
                'user_id' => Auth::id() ?: 1,
                'sale_date' => Carbon::now('America/La_Paz'),
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
                if ($product && ($product->item_type === 'COMBO' || (!$product->isService() && $product->stock > 0))) {
                    $qtyToDeduct = $product->item_type === 'COMBO' ? $item->quantity : min($product->stock, $item->quantity);
                    $product->substract_stock(
                        $qtyToDeduct,
                        'SALIDA_VENTA',
                        $refCode,
                        "Venta en restaurante ({$order->order_number})",
                        $sale->branch_id
                    );
                }

                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => intval($item->quantity),
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

            DB::commit();

            Alert::toast("Cuenta {$order->order_number} cobrada con éxito. Mesa liberada.", 'success');
            return redirect()->route('sales.ticket', ['sale' => $sale->id]);
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                try {
                    DB::rollBack();
                } catch (\Throwable $rb) {}
            }
            \Illuminate\Support\Facades\Log::error("Error al cobrar cuenta restaurante: " . $e->getMessage(), [
                'order' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);
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

