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
}
