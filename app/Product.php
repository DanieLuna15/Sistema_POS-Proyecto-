<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\BelongsToCompany;

class Product extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'code',
        'sku',
        'name',
        'item_type',
        'unit_measure',
        'stock',
        'stock_min',
        'location',
        'batch_number',
        'expiration_date',
        'warranty_days',
        'description',
        'image',
        'purchase_price',
        'sell_price',
        'wholesale_price',
        'wholesale_min_qty',
        'status',
        'category_id',
        'brand_id',
        'provider_id',
        'branch_id',
    ];

    protected $casts = [
        'purchase_price' => 'float',
        'sell_price' => 'float',
        'wholesale_price' => 'float',
        'stock' => 'integer',
        'stock_min' => 'integer',
    ];

    public function isService()
    {
        if (in_array($this->item_type, ['SERVICIO', 'COMBO'])) {
            return true;
        }

        // En modo RESTAURANTE, los platos preparados suelen registrarse sin stock de almacén (stock = 0)
        try {
            $company = Company::current();
            if ($company && $company->business_type === 'RESTAURANTE') {
                $rawStock = intval($this->attributes['stock'] ?? 0);
                if ($rawStock <= 0) {
                    return true;
                }
            }
        } catch (\Throwable $e) {}

        return false;
    }

    public function branchStocks()
    {
        return $this->hasMany(BranchProduct::class, 'product_id');
    }

    public function comboItems()
    {
        return $this->hasMany(ProductComboItem::class, 'combo_product_id')->with('component');
    }

    /**
     * Obtiene el stock exclusivo de una sucursal (por defecto la Sucursal Activa en sesión).
     */
    public function stockInBranch($branchId = null)
    {
        if ($this->isService()) {
            return 0;
        }

        try {
            $targetBranchId = $branchId ?: optional(Branch::current())->id;
            if (!$targetBranchId) {
                return intval($this->attributes['stock'] ?? 0);
            }

            if ($this->relationLoaded('branchStocks')) {
                $record = $this->branchStocks->firstWhere('branch_id', $targetBranchId);
                if ($record) {
                    return intval($record->stock);
                }
                if ($this->branchStocks->isEmpty() && ($this->branch_id == $targetBranchId || !$this->branch_id)) {
                    return intval($this->attributes['stock'] ?? 0);
                }
                return 0;
            }

            $record = BranchProduct::where('product_id', $this->id)
                ->where('branch_id', $targetBranchId)
                ->first();

            if ($record) {
                return intval($record->stock);
            }

            // Si aún no tiene ningún registro en branch_products y es su sucursal base
            $anyBranchRecord = BranchProduct::where('product_id', $this->id)->exists();
            if (!$anyBranchRecord && ($this->branch_id == $targetBranchId || !$this->branch_id)) {
                return intval($this->attributes['stock'] ?? 0);
            }

            return 0;
        } catch (\Throwable $e) {
            return intval($this->attributes['stock'] ?? 0);
        }
    }

    /**
     * Accesor inteligente: $product->stock devuelve automáticamente el stock de la Sucursal Activa.
     */
    public function getStockAttribute($value)
    {
        if (!$this->exists) {
            return intval($value ?? 0);
        }
        return $this->stockInBranch();
    }

    /**
     * Stock Total Global sumando todas las sucursales de la empresa.
     */
    public function getTotalCompanyStockAttribute()
    {
        if ($this->relationLoaded('branchStocks') && $this->branchStocks->isNotEmpty()) {
            return intval($this->branchStocks->sum('stock'));
        }
        return intval($this->attributes['stock'] ?? 0);
    }

    /**
     * Sincroniza la columna global products.stock con la suma de todas las sucursales.
     */
    public function syncGlobalStock()
    {
        try {
            $total = BranchProduct::where('product_id', $this->id)->sum('stock');
            \Illuminate\Support\Facades\DB::table('products')
                ->where('id', $this->id)
                ->update(['stock' => intval($total)]);
            $this->attributes['stock'] = intval($total);
        } catch (\Throwable $e) {}
    }

    public function add_stock($quantity, $movementType = 'ENTRADA_COMPRA', $referenceCode = null, $notes = null, $unitCost = null, $branchId = null)
    {
        if ($this->item_type === 'COMBO') {
            try {
                foreach ($this->comboItems as $ci) {
                    if ($ci->component && !$ci->component->isService()) {
                        $ci->component->add_stock(
                            floatval($quantity) * floatval($ci->quantity),
                            $movementType,
                            $referenceCode,
                            ($notes ?: 'Reingreso') . " (Componente de Combo: {$this->name})",
                            null,
                            $branchId
                        );
                    }
                }
            } catch (\Throwable $e) {}
            return;
        }

        if ($this->isService()) {
            return;
        }

        $cid = $this->company_id ?: Company::currentId() ?: 1;
        $targetBranchId = $branchId 
            ?: optional(Branch::current())->id 
            ?: optional(Branch::where('company_id', $cid)->where('status', 'ACTIVO')->where('is_main', true)->first())->id
            ?: optional(Branch::where('company_id', $cid)->where('status', 'ACTIVO')->first())->id
            ?: 1;
        $qty = floatval($quantity);

        $bp = BranchProduct::firstOrCreate(
            [
                'branch_id' => $targetBranchId,
                'product_id' => $this->id,
            ],
            [
                'company_id' => $this->company_id ?: Company::currentId(),
                'stock' => 0,
                'stock_min' => $this->stock_min ?? 5,
                'location' => $this->location,
            ]
        );

        $previousStock = floatval($bp->stock);
        $newStock = $previousStock + $qty;
        $bp->update(['stock' => intval($newStock)]);

        $this->syncGlobalStock();

        if ($unitCost !== null && floatval($unitCost) > 0) {
            $this->update(['purchase_price' => floatval($unitCost)]);
        }

        try {
            InventoryMovement::create([
                'company_id' => $this->company_id ?: Company::currentId(),
                'product_id' => $this->id,
                'branch_id' => $targetBranchId,
                'user_id' => Auth::id(),
                'movement_type' => $movementType,
                'quantity' => $qty,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'unit_cost' => $unitCost ?? $this->purchase_price ?? 0,
                'reference_code' => $referenceCode,
                'notes' => $notes ?? 'Ingreso de stock al inventario de la sucursal',
                'movement_date' => Carbon::now('America/La_Paz'),
            ]);
        } catch (\Throwable $e) {
            // Continuar aunque la tabla de kardex no esté migrada aún
        }
    }

    public function substract_stock($quantity, $movementType = 'SALIDA_VENTA', $referenceCode = null, $notes = null, $branchId = null)
    {
        // Si es un COMBO, descontar automáticamente del Kardex únicamente sus componentes físicos (ej. gaseosas/bebidas)
        if ($this->item_type === 'COMBO') {
            try {
                foreach ($this->comboItems as $ci) {
                    if ($ci->component && !$ci->component->isService() && $ci->component->stock > 0) {
                        $needed = floatval($quantity) * floatval($ci->quantity);
                        $ci->component->substract_stock(
                            min($ci->component->stock, $needed),
                            $movementType,
                            $referenceCode,
                            ($notes ?: 'Venta') . " [Combo: {$this->name}]",
                            $branchId
                        );
                    }
                }
            } catch (\Throwable $e) {}
            return;
        }

        if ($this->isService()) {
            return;
        }

        $cid = $this->company_id ?: Company::currentId() ?: 1;
        $targetBranchId = $branchId 
            ?: optional(Branch::current())->id 
            ?: optional(Branch::where('company_id', $cid)->where('status', 'ACTIVO')->where('is_main', true)->first())->id
            ?: optional(Branch::where('company_id', $cid)->where('status', 'ACTIVO')->first())->id
            ?: 1;
        $qty = floatval($quantity);

        $bp = BranchProduct::firstOrCreate(
            [
                'branch_id' => $targetBranchId,
                'product_id' => $this->id,
            ],
            [
                'company_id' => $this->company_id ?: Company::currentId(),
                'stock' => intval($this->attributes['stock'] ?? 0),
                'stock_min' => $this->stock_min ?? 5,
                'location' => $this->location,
            ]
        );

        $previousStock = floatval($bp->stock);
        $newStock = max(0, $previousStock - $qty);
        $bp->update(['stock' => intval($newStock)]);

        $this->syncGlobalStock();

        try {
            InventoryMovement::create([
                'company_id' => $this->company_id ?: Company::currentId(),
                'product_id' => $this->id,
                'branch_id' => $targetBranchId,
                'user_id' => Auth::id(),
                'movement_type' => $movementType,
                'quantity' => $qty,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'unit_cost' => $this->purchase_price ?? 0,
                'reference_code' => $referenceCode,
                'notes' => $notes ?? 'Salida de stock del inventario de la sucursal',
                'movement_date' => Carbon::now('America/La_Paz'),
            ]);
        } catch (\Throwable $e) {
            // Continuar aunque la tabla de kardex no esté migrada aún
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class)->orderBy('movement_date', 'DESC');
    }

    public function getProfitMarginAttribute()
    {
        if ($this->purchase_price <= 0) {
            return null;
        }
        return round((($this->sell_price - $this->purchase_price) / $this->purchase_price) * 100, 1);
    }

    public static function unitsCatalog()
    {
        return [
            'UND' => 'Unidad (UND)',
            'CAJA' => 'Caja (CAJA)',
            'PAQ' => 'Paquete (PAQ)',
            'KG' => 'Kilogramo (KG)',
            'GR' => 'Gramo (GR)',
            'LT' => 'Litro (LT)',
            'MT' => 'Metro (MT)',
            'DOC' => 'Docena (DOC)',
            'PAR' => 'Par (PAR)',
            'BOLSA' => 'Bolsa (BOLSA)',
            'FRASCO' => 'Frasco / Blíster',
            'ROLLO' => 'Rollo (ROLLO)',
            'SERV' => 'Servicio / Mano de Obra (SERV)',
            'HR' => 'Hora (HR)',
        ];
    }
}
