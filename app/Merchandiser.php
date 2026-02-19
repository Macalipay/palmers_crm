<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchandiser extends Model
{
    protected $fillable = [
        'merchandiser',
        'active',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
}
