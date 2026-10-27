<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransferDetail extends Model
{
    protected $table = 'stock_transfer_details';

    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'quantity',
        'unit_cost',
    ];

    public function transfer()
    {
        return $this->belongsTo(StockTransfer::class, 'stock_transfer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

