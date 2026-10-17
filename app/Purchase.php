<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
    protected $fillable = [
        'provider_id',
        'user_id',
        'purchase_date',
        'tax',
        'total',
        'status',
        'picture',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function provider(){
        return $this->belongsTo(Provider::class);
    }

    public function PurchaseDetails(){
        return $this->hasMany(PurchaseDetails::class);
    }
    public function update_stock($id, $quantity){
        $product = Product::findOrFail($id);
        $product->add_stock($quantity);
    }

    public function my_store($request){
        return DB::transaction(function() use ($request) {
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

            $purchase = Purchase::create([
                'provider_id' => $request->provider_id,
                'user_id' => Auth::user()->id,
                'purchase_date' => Carbon::now('America/La_Paz'),
                'tax' => $tax,
                'total' => round($totalConImpuesto, 2),
                'status' => 'CONFIRMADO',
                'picture' => $picture_name,
            ]);

            $purchase->add_purchase_details($request);
            return $purchase;
        });
    }

    public function add_purchase_details($request){
        $results = [];
        if ($request->has('product_id')) {
            foreach ($request->product_id as $key => $productId){
                $qty = floatval($request->quantity[$key]);
                $this->update_stock($productId, $qty);
                $results[] = array(
                    "product_id" => $productId,
                    "quantity" => $qty,
                    "price" => floatval($request->price[$key])
                );
            }
            if (!empty($results)) {
                $this->PurchaseDetails()->createMany($results);
            }
        }
    }
}
