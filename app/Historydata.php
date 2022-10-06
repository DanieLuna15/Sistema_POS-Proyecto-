<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historydata extends Model
{
    protected $fillable = [
        'month_date',
        'total_bs',
        'category_id',
        'quantity',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
