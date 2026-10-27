<?php

namespace App\Traits;

use App\Company;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    public static function bootBelongsToCompany()
    {
        // Filtrar automáticamente todas las consultas por la empresa activa del usuario autenticado
        static::addGlobalScope('company_tenant', function (Builder $builder) {
            $companyId = Company::currentId();
            if ($companyId) {
                $table = $builder->getModel()->getTable();
                $builder->where(function ($q) use ($table, $companyId) {
                    $q->where("{$table}.company_id", $companyId)
                      ->orWhereNull("{$table}.company_id");
                });
            }
        });

        // Al crear cualquier registro, inyectar automáticamente el company_id del negocio activo
        static::creating(function ($model) {
            if (empty($model->company_id)) {
                $companyId = Company::currentId();
                if ($companyId) {
                    $model->company_id = $companyId;
                }
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

