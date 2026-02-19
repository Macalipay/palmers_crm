<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'branch_name',
        'address',
        'active',
        'created_by',
        'updated_by',
        'deleted_at',
        'division_id'
    ];

    public function sale()
    {
        return $this->hasMany(Sale::class, 'branch_id', 'id');
    }
}
