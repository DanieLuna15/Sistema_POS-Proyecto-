<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Brand extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'name', 'description',
    ];
    public function products(){
        return $this->HasMany(Product::class);
    }
}
