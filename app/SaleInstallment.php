<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleInstallment extends Model
{
    protected $fillable = [
        'sale_id',
        'installment_number',
        'due_date',
        'amount',
        'paid_amount',
        'status',
        'payment_date',
        'payment_method',
        'payment_method_id',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
