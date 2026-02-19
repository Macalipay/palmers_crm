<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'item_name',
        'description',
        'division_id',
        'amount',
        'active',
        'created_by',
        'updated_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function duration()
    {
        return $this->hasOne(ItemDuration::class, 'item_id');
    }
}
