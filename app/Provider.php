<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Provider extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'name', 'email', 'nit', 'address', 'phone',
    ];
    public function products(){
        return $this->HasMany(Product::class);
    }
}
