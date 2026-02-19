<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleAttachment extends Model
{
    protected $fillable = [
        'sale_id',
        'filename'
    ];
}
