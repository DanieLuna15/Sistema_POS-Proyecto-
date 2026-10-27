<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrderItem extends Model
{
    protected $table = 'restaurant_order_items';

    protected $fillable = [
        'restaurant_order_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'kitchen_status',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
