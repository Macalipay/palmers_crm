<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Yajra\Address\HasAddress;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasAddress;
    use HasRoles;

    protected $fillable = [
        'name',
        'designation',
        'email',
        'contact_number',
        'password',
        'picture',
        'birthday',
        'active',
        'division_id',
        'branch_id',
        'created_by',
        'updated_by'
    ];

    public function role()
    {
        return $this->belongsTo(ModelHasRoles::class, 'id', 'model_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    
    public function agent()
    {
        return $this->hasMany(Sale::class, 'user_id', 'id');
    }
}
