<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = [
        'source',
        'active',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
    
    public function telemarketing()
    {
        return $this->hasMany(Telemarketing::class, 'source_id', 'id');
    }
    
    public function Source()
    {
        return $this->hasMany(Sale::class, 'source_id', 'id');
    }
}
