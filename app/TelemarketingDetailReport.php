<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelemarketingDetailReport extends Model
{
    protected $fillable = [
        'telemarketing_detail_id',
        'reported_by',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    public function telemarketingDetail()
    {
        return $this->belongsTo(TelemarketingDetail::class, 'telemarketing_detail_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
