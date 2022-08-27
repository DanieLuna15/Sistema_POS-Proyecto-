<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name', 'description',
    ];
    public function products(){
        return $this->HasMany(Product::class);
    }
}
