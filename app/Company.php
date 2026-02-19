<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'contact_person',
        'contact_no',
        'address',
        'province_id',
        'industry',
        'tin',
        'business_style',
        'active',
        'created_by',
        'updated_by',
        'deleted_at'
    ];

    public function province()
    {
        return $this->belongsTo(ProvinceName::class, 'province_id');
    }

    public function industry()
    {
        return $this->hasMany(Sale::class, 'company_id', 'id');
    }
}
