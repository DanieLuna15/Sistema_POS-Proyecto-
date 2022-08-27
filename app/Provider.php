<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'name', 'email', 'nit', 'address', 'phone',
    ];
    public function products(){
        return $this->HasMany(Product::class);
    }
}
