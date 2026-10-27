<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class BranchProduct extends Model
{
    use BelongsToCompany;

    protected $table = 'branch_products';

    protected $fillable = [
        'company_id',
        'branch_id',
        'product_id',
        'stock',
        'stock_min',
        'location',
    ];

    protected $casts = [
        'stock' => 'integer',
        'stock_min' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

