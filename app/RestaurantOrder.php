<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class RestaurantOrder extends Model
{
    use BelongsToCompany;

    protected $table = 'restaurant_orders';

    protected $fillable = [
        'company_id',
        'branch_id',
        'restaurant_table_id',
        'user_id',
        'client_id',
        'sale_id',
        'order_number',
        'service_type',
        'diners_count',
        'customer_name',
        'delivery_phone',
        'delivery_address',
        'status',
        'subtotal',
        'discount',
        'tip_amount',
        'total',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'subtotal' => 'float',
        'discount' => 'float',
        'tip_amount' => 'float',
        'total' => 'float',
    ];

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function items()
    {
        return $this->hasMany(RestaurantOrderItem::class, 'restaurant_order_id');
    }

    /**
     * Recalcula subtotal y total de la comanda en base a sus platos/bebidas.
     */
    public function recalculate()
    {
        $sub = 0;
        foreach ($this->items()->get() as $item) {
            $line = ($item->quantity * $item->price) - (($item->quantity * $item->price * $item->discount) / 100);
            $sub += max(0, $line);
        }

        $this->subtotal = round($sub, 2);
        $this->total = max(0, round($sub - floatval($this->discount) + floatval($this->tip_amount), 2));
        $this->save();

        return $this;
    }
}

