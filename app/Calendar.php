<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $fillable = [
        'title',
        'program_id',
        'start',
        'end',
        'color',
        'description',
        'location',
        'reminder',
        'status',
        'company_id',
        'created_by',
        'updated_by',
    ];
}
