<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'brand',
        'active',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
}
