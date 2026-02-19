<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSystemSetup extends Model
{
    protected $fillable = [
        'sales_id',
        'product',
        'type',
        'indx',
        'other_value',
    ];
    
    public function sales()
    {
        return $this->belongsTo(ASDSale::class, 'sales_id');
    }
}
