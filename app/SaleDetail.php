<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{

    protected $fillable = [
        'sale_id',
        'item_id',
        'brand_id',
        'warranty_no',
        'serial_no',
        'quantity',
        'amount',
        'discount',
        'description',
        'total',
        'active',
        'created_by',
        'updated_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}
