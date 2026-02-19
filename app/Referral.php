<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'name',
        'placement_id',
        'note_address',
        'contact_no',
        'company_name',
        'location',
        'location_id',
        'status',
        'outcome',
        'text',
        'active',
        'image',
        'collapsable',
    ];

    public function referral()
    {
        return $this->hasOne(Referral::class, 'id', 'placement_id');
    }
}
