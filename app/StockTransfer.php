<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class StockTransfer extends Model
{
    use BelongsToCompany;

    protected $table = 'stock_transfers';

    protected $fillable = [
        'company_id',
        'from_branch_id',
        'to_branch_id',
        'user_id',
        'transfer_date',
        'reference_code',
        'status',
        'notes',
    ];

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(StockTransferDetail::class, 'stock_transfer_id');
    }
}

