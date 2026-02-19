<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesAssociate extends Model
{
    protected $fillable = [
        'sales_associate',
        'active',
        'created_by',
        'updated_by',
        'deleted_at'
    ];

    public function associate()
    {
        return $this->hasMany(Sale::class, 'sales_associate_id', 'id');
    }
}
