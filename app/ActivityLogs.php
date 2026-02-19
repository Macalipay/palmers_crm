<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'ip_address',
        'device_info',
        'details'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
