<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemDuration extends Model
{
    protected $fillable = [
        'item_id',
        'brandnew',
        'refill',
        'for_warranty',
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
}
