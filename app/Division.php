<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'division',
        'active',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
    
    public function division()
    {
        return $this->hasMany(Sale::class, 'division_id', 'id');
    }
}
