<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Quotation extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'branch_id',
        'client_id',
        'user_id',
        'quotation_date',
        'valid_until',
        'tax',
        'subtotal',
        'discount_total',
        'total',
        'status',
        'converted_sale_id',
        'notes',
        'terms',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function details()
    {
        return $this->hasMany(QuotationDetail::class);
    }

    public function convertedSale()
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }
}

