<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductComboItem extends Model
{
    protected $table = 'product_combo_items';

    protected $fillable = [
        'combo_product_id',
        'component_product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function combo()
    {
        return $this->belongsTo(Product::class, 'combo_product_id');
    }

    public function component()
    {
        return $this->belongsTo(Product::class, 'component_product_id');
    }

    public function componentProduct()
    {
        return $this->belongsTo(Product::class, 'component_product_id');
    }
}
