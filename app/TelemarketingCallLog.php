<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelemarketingCallLog extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'sale_id',
        'telemarketing_detail_id',
        'new_order_id',
        'total_amount',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function telemarketing_detail()
    {
        return $this->belongsTo(TelemarketingDetail::class, 'telemarketing_detail_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
