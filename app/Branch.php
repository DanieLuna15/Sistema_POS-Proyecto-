<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToCompany;

class Branch extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'manager_name',
        'phone',
        'email',
        'address',
        'city',
        'authorization_number',
        'is_main',
        'status',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVO');
    }

    /**
     * Obtiene la sucursal activa perteneciente a la empresa actual del usuario.
     */
    public static function current()
    {
        try {
            $companyId = Company::currentId();

            $branchId = session('active_branch_id');
            if ($branchId) {
                $branch = self::where('company_id', $companyId)->find($branchId);
                if ($branch) {
                    return $branch;
                }
            }

            if (Auth::check() && Auth::user()->branch_id) {
                $branch = self::where('company_id', $companyId)->find(Auth::user()->branch_id);
                if ($branch) {
                    return $branch;
                }
            }

            $mainBranch = self::where('company_id', $companyId)->where('is_main', true)->first()
                       ?? self::where('company_id', $companyId)->first();

            return $mainBranch;
        } catch (\Throwable $e) {
            return new self([
                'code' => 'SUC-01',
                'name' => 'Casa Matriz - Central',
                'address' => 'Av. Heroínas #742 esq. Ayacucho',
                'city' => 'Cochabamba',
                'phone' => '(+591) 4-4589210',
                'is_main' => true,
                'status' => 'ACTIVO',
            ]);
        }
    }
}
