<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesSerialNo extends Model
{
    protected $fillable = [
        'sale_details_id',
        'serial_no',
        'warranty_no',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $table = 'sales_serial_no';
}
