<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'stock',
        'image',
        'sell_price',
        'status',
        'category_id',
        'brand_id',
        'provider_id',
    ];

    public function add_stock($quantity){
        $this->increment('stock',$quantity);
    }
    public function substract_stock($quantity){
        $this->decrement('stock',$quantity);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function provider(){
        return $this->belongsTo(Provider::class);
    }
}
