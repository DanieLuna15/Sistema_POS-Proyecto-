<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class Sale extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'sale_date',
        'tax',
        'total',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function saleDetails(){
        return $this->hasMany(SaleDetails::class);
    }
    public function update_stock($id,$quantity){
        $product=Product::find($id);
        $product->substract_stock($quantity);
    }
    public function my_store($request){
        $sale=Sale::create($request->all()+[
            'user_id'=>Auth::user()->id,
            'sale_date'=>Carbon::now('America/La_Paz'),
        ]);
        $sale->add_sale_details($request);
    }
    public function add_sale_details($request){
        foreach($request->product_id as $key=>$product){
            $this->update_stock($request->product_id[$key],$request->quantity[$key]);
            $results[]= array(
                "product_id"=>$request->product_id[$key],
                "quantity"=>$request->quantity[$key],
                "price"=>$request->price[$key],
                "discount"=>$request->discount[$key],
            );
        }
        $this->saleDetails()->createMany($results);
    }
}
