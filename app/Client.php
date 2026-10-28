<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Client extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'ci',
        'nit',
        'address',
        'phone',
        'email',
    ];
    public function sales(){
        return $this->hasMany(Sale::class);
    }
    public function purchases(){
        return $this->hasMany(Purchase::class);
    }

    /**
     * Garantiza que la empresa activa tenga al menos un cliente "SIN NOMBRE (CONSUMIDOR FINAL)"
     * para ventas rápidas en Restaurante, POS o Mostrador.
     */
    public static function ensureDefaultForCompany($companyId = null)
    {
        $cid = $companyId ?: Company::currentId();
        $first = static::orderBy('id', 'ASC')->first();
        if ($first) {
            return $first;
        }

        return static::create([
            'company_id' => $cid,
            'name' => 'SIN NOMBRE (CONSUMIDOR FINAL)',
            'ci' => '0',
            'nit' => '0',
            'address' => 'Mostrador',
            'phone' => '00000000',
        ]);
    }
}
