<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\BelongsToCompany;

class Purchase extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'provider_id',
        'user_id',
        'branch_id',
        'cash_register_id',
        'purchase_date',
        'tax',
        'total',
        'status',
        'picture',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function PurchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class);
    }

    public function update_stock($id, $quantity, $referenceCode = null, $unitCost = null)
    {
        $product = Product::findOrFail($id);
        $product->add_stock($quantity, 'ENTRADA_COMPRA', $referenceCode, 'Abastecimiento por Compra', $unitCost);
    }

    public function my_store($request)
    {
        return DB::transaction(function () use ($request) {
            $picture_name = null;
            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $picture_name = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                $file->move(public_path("/image"), $picture_name);
            }

            // Calcular total en servidor
            $totalCalculado = 0;
            if ($request->has('product_id')) {
                foreach ($request->product_id as $key => $productId) {
                    $qty = floatval($request->quantity[$key]);
                    $price = floatval($request->price[$key]);
                    $totalCalculado += ($qty * $price);
                }
            }

            $tax = floatval($request->tax ?? 0);
            $totalConImpuesto = $totalCalculado + ($totalCalculado * ($tax / 100));

            $activeBranch = Branch::current();
            $openRegister = CashRegister::activeOpen();

            $purchase = Purchase::create([
                'provider_id' => $request->provider_id,
                'user_id' => Auth::user()->id,
                'branch_id' => $request->branch_id ?? ($activeBranch ? $activeBranch->id : null),
                'cash_register_id' => $openRegister ? $openRegister->id : null,
                'purchase_date' => Carbon::now('America/La_Paz'),
                'tax' => $tax,
                'total' => round($totalConImpuesto, 2),
                'status' => 'CONFIRMADO',
                'picture' => $picture_name,
                'notes' => $request->notes ?? null,
            ]);

            $purchase->add_purchase_details($request);
            return $purchase;
        });
    }

    public function add_purchase_details($request)
    {
        $results = [];
        $refCode = 'CMP-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        if ($request->has('product_id')) {
            foreach ($request->product_id as $key => $productId) {
                $qty = floatval($request->quantity[$key]);
                $price = floatval($request->price[$key]);
                $this->update_stock($productId, $qty, $refCode, $price);
                $results[] = array(
                    "product_id" => $productId,
                    "quantity" => $qty,
                    "price" => $price,
                );
            }
            if (!empty($results)) {
                $this->PurchaseDetails()->createMany($results);
            }
        }
    }
}
