<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class RestaurantTable extends Model
{
    use BelongsToCompany;

    protected $table = 'restaurant_tables';

    protected $fillable = [
        'company_id',
        'branch_id',
        'name',
        'zone',
        'capacity',
        'shape',
        'pos_x',
        'pos_y',
        'status',
        'active_order_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function activeOrder()
    {
        return $this->belongsTo(RestaurantOrder::class, 'active_order_id');
    }

    public function orders()
    {
        return $this->hasMany(RestaurantOrder::class, 'restaurant_table_id');
    }

    /**
     * Crea mesas iniciales por defecto para la sucursal activa si aún no existen.
     */
    public static function ensureDefaultTables($companyId, $branchId)
    {
        $exists = self::withoutGlobalScope('company_tenant')
            ->where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->exists();

        if (!$exists && $branchId) {
            $defaults = [
                ['name' => 'Mesa 1', 'zone' => 'Salón Principal', 'capacity' => 4],
                ['name' => 'Mesa 2', 'zone' => 'Salón Principal', 'capacity' => 4],
                ['name' => 'Mesa 3', 'zone' => 'Salón Principal', 'capacity' => 4],
                ['name' => 'Mesa 4', 'zone' => 'Salón Principal', 'capacity' => 6],
                ['name' => 'Mesa 5', 'zone' => 'Salón Principal', 'capacity' => 2],
                ['name' => 'Mesa 6', 'zone' => 'Salón Principal', 'capacity' => 6],
                ['name' => 'Terraza 1', 'zone' => 'Terraza / Exterior', 'capacity' => 4],
                ['name' => 'Barra 1', 'zone' => 'Barra Rápida', 'capacity' => 2],
            ];

            foreach ($defaults as $t) {
                self::create([
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                    'name' => $t['name'],
                    'zone' => $t['zone'],
                    'capacity' => $t['capacity'],
                    'status' => 'LIBRE',
                ]);
            }
        }
    }
}

