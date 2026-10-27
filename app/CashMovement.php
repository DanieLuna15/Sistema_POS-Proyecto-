<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class CashMovement extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cash_register_id',
        'branch_id',
        'user_id',
        'type',
        'category',
        'amount',
        'payment_method',
        'reference',
        'description',
        'movement_date',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

