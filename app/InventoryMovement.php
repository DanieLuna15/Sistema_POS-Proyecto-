<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class InventoryMovement extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'product_id',
        'branch_id',
        'user_id',
        'movement_type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'reference_code',
        'notes',
        'movement_date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isEntry()
    {
        return in_array($this->movement_type, [
            'ENTRADA_COMPRA',
            'ANULACION_VENTA',
            'AJUSTE_ENTRADA',
            'INVENTARIO_INICIAL',
            'TRASPASO_ENTRADA',
        ]);
    }
}

