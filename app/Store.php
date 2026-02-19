<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'company_id',
        'code',
        'store_name',
        'contact',
        'address',
        'remarks'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
