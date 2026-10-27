<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class PaymentMethod extends Model
{
    use BelongsToCompany;

    protected $table = 'payment_methods';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'payment_condition',
        'description',
        'instructions',
        'requires_reference',
        'icon',
        'color',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'requires_reference' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function installments()
    {
        return $this->hasMany(SaleInstallment::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVO')->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');
    }

    public function scopeForContado($query)
    {
        return $query->whereIn('payment_condition', ['CONTADO', 'AMBOS'])->where('status', 'ACTIVO');
    }

    public function scopeForCredito($query)
    {
        return $query->whereIn('payment_condition', ['CREDITO', 'AMBOS'])->where('status', 'ACTIVO');
    }

    /**
     * Badges Helpers
     */
    public function getConditionBadgeAttribute()
    {
        if ($this->payment_condition === 'CONTADO') {
            return '<span class="badge badge-success px-2 py-1"><i class="fas fa-money-bill-wave mr-1"></i> CONTADO</span>';
        } elseif ($this->payment_condition === 'CREDITO') {
            return '<span class="badge badge-info px-2 py-1"><i class="fas fa-hand-holding-usd mr-1"></i> CRÉDITO</span>';
        } else {
            return '<span class="badge badge-primary px-2 py-1"><i class="fas fa-exchange-alt mr-1"></i> AMBOS (CONT./CRÉD.)</span>';
        }
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status === 'ACTIVO') {
            return '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> ACTIVO</span>';
        }
        return '<span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> INACTIVO</span>';
    }
}
