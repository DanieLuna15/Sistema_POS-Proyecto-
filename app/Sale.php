<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\BelongsToCompany;

class Sale extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'client_id',
        'user_id',
        'branch_id',
        'cash_register_id',
        'sale_date',
        'total',
        'status',
        'sale_type',
        'payment_method',
        'payment_method_id',
        'payment_condition',
        'payment_reference',
        'cash_received',
        'cash_change',
        'down_payment',
        'balance_pending',
        'credit_status',
        'installments_count',
        'installment_frequency',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class);
    }

    public function installments()
    {
        return $this->hasMany(SaleInstallment::class)->orderBy('installment_number', 'ASC');
    }

    public function update_stock($id, $quantity, $referenceCode = null)
    {
        $product = Product::findOrFail($id);
        $isRestaurant = optional(Company::current())->business_type === 'RESTAURANTE';
        if (!$product->isService() && !$isRestaurant && $product->stock < $quantity) {
            throw new \Exception("Stock insuficiente para el producto '{$product->name}'. Stock actual: {$product->stock}, Cantidad solicitada: {$quantity}.");
        }
        if ($product->item_type === 'COMBO') {
            $product->substract_stock($quantity, 'SALIDA_VENTA', $referenceCode, 'Venta de combo');
        } elseif (!$product->isService() && $product->stock > 0) {
            $product->substract_stock(min($product->stock, $quantity), 'SALIDA_VENTA', $referenceCode, 'Venta de mercadería');
        }
    }

    public function my_store($request)
    {
        return DB::transaction(function () use ($request) {
            // 1. Validar existencias de stock y calcular el total en el servidor
            $totalCalculado = 0;
            $isRestaurant = optional(Company::current())->business_type === 'RESTAURANTE';
            if ($request->has('product_id')) {
                foreach ($request->product_id as $key => $productId) {
                    $qty = floatval($request->quantity[$key]);
                    $price = floatval($request->price[$key]);
                    $discount = isset($request->discount[$key]) ? floatval($request->discount[$key]) : 0;

                    $product = Product::findOrFail($productId);
                    if (!$product->isService() && !$isRestaurant && $product->stock < $qty) {
                        throw new \Exception("Stock insuficiente para el producto '{$product->name}'. Disponible: {$product->stock}, solicitado: {$qty}.");
                    }

                    $subtotal = ($qty * $price) - (($qty * $price * $discount) / 100);
                    $totalCalculado += max(0, $subtotal);
                }
            }

            $saleType = $request->sale_type ?? 'REGULAR';
            $paymentCondition = $request->payment_condition ?? ($saleType === 'CREDITO' ? 'CREDITO' : 'CONTADO');

            $paymentMethodId = $request->payment_method_id ?? null;
            $paymentMethodModel = $paymentMethodId ? PaymentMethod::find($paymentMethodId) : null;
            $paymentMethodName = $paymentMethodModel ? $paymentMethodModel->name : ($request->payment_method ?? ($saleType === 'CREDITO' ? 'Crédito Directo en Tienda' : 'Efectivo (Bs.)'));
            $paymentReference = $request->payment_reference ?? null;

            $cashReceived = $request->filled('cash_received') ? floatval($request->cash_received) : null;
            $cashChange = $cashReceived ? max(0, $cashReceived - $totalCalculado) : null;

            $downPayment = floatval($request->down_payment ?? 0);
            $balancePending = ($saleType === 'CREDITO') ? max(0, $totalCalculado - $downPayment) : 0;
            $creditStatus = ($saleType === 'CREDITO') ? ($balancePending <= 0 ? 'PAGADO' : 'PENDIENTE') : null;
            $installmentsCount = ($saleType === 'CREDITO') ? intval($request->installments_count ?? 1) : null;
            $installmentFrequency = ($saleType === 'CREDITO') ? ($request->installment_frequency ?? 'MENSUAL') : null;

            $activeBranch = Branch::current();
            $openRegister = CashRegister::activeOpen();

            // 2. Crear la venta con el total verificado por el servidor
            $sale = Sale::create([
                'client_id' => $request->client_id,
                'user_id' => Auth::user()->id,
                'branch_id' => $request->branch_id ?? ($activeBranch ? $activeBranch->id : null),
                'cash_register_id' => $openRegister ? $openRegister->id : null,
                'sale_date' => Carbon::now('America/La_Paz'),
                'total' => round($totalCalculado, 2),
                'status' => 'CONFIRMADO',
                'sale_type' => $saleType,
                'payment_method' => $paymentMethodName,
                'payment_method_id' => $paymentMethodId,
                'payment_condition' => $paymentCondition,
                'payment_reference' => $paymentReference,
                'cash_received' => $cashReceived,
                'cash_change' => $cashChange,
                'down_payment' => $downPayment,
                'balance_pending' => round($balancePending, 2),
                'credit_status' => $creditStatus,
                'installments_count' => $installmentsCount,
                'installment_frequency' => $installmentFrequency,
                'notes' => $request->notes ?? null,
            ]);

            // 3. Registrar los detalles y actualizar el inventario con trazabilidad en Kardex
            $sale->add_sale_details($request);

            // 4. Si es venta a crédito, generar el cronograma de cuotas
            if ($sale->sale_type === 'CREDITO' && $sale->balance_pending > 0 && $installmentsCount > 0) {
                $installmentAmount = round($sale->balance_pending / $installmentsCount, 2);
                $currentDate = Carbon::now('America/La_Paz');
                $installmentsData = [];

                for ($i = 1; $i <= $installmentsCount; $i++) {
                    $dueDate = clone $currentDate;
                    if ($installmentFrequency === 'SEMANAL') {
                        $dueDate->addWeeks($i);
                    } elseif ($installmentFrequency === 'QUINCENAL') {
                        $dueDate->addDays($i * 15);
                    } else {
                        $dueDate->addMonths($i);
                    }

                    $amount = ($i === $installmentsCount)
                        ? ($sale->balance_pending - ($installmentAmount * ($installmentsCount - 1)))
                        : $installmentAmount;

                    $installmentsData[] = [
                        'installment_number' => $i,
                        'due_date' => $dueDate->toDateString(),
                        'amount' => max(0, round($amount, 2)),
                        'paid_amount' => 0,
                        'status' => 'PENDIENTE',
                    ];
                }
                $sale->installments()->createMany($installmentsData);
            }

            // 5. Si hay una caja abierta, actualizar sus totales
            if ($openRegister) {
                try {
                    $openRegister->recalculateTotals();
                } catch (\Throwable $e) {}
            }

            return $sale;
        });
    }

    public function add_sale_details($request)
    {
        $results = [];
        $refCode = 'VTA-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        if ($request->has('product_id')) {
            foreach ($request->product_id as $key => $productId) {
                $qty = floatval($request->quantity[$key]);
                $this->update_stock($productId, $qty, $refCode);
                $results[] = array(
                    "product_id" => $productId,
                    "quantity" => $qty,
                    "price" => floatval($request->price[$key]),
                    "discount" => isset($request->discount[$key]) ? floatval($request->discount[$key]) : 0,
                );
            }
            if (!empty($results)) {
                $this->saleDetails()->createMany($results);
            }
        }
    }
}
