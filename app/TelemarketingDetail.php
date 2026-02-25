<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelemarketingDetail extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'telemarketing_id',
        'date',
        'task',
        'order_id',
        'new_order_id',
        'total_amount',
        'description',
        'lead_status',
        'remarks',
        'assigned_to',
        'status',
        'branch_id',
        'remarks',
        'active',
        'created_by',
        'updated_by',
        'deleted_at',
        'created_at',
        'updated_at',
        'call_duration',
        'assigned_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function telemarketing()
    {
        return $this->belongsTo(Telemarketing::class, 'telemarketing_id');
    }

    public function csd()
    {
        return $this->belongsTo(SaleDetail::class, 'order_id');
    }
}
