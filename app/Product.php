<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
