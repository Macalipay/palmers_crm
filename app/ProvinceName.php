<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProvinceName extends Model
{
    protected $fillable = [
        'province',
        'active',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
}
