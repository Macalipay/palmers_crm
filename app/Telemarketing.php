<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Telemarketing extends Model
{
    protected $fillable = [
        'company_id',
        'lead_status',
        'opportunity_status',
        'source_id',
        'product_interest',
        'active',
        'created_by',
        'updated_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }
}
