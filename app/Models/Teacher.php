<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Junaidnasir\Larainvite\InviteTrait;

class Teacher extends Authenticatable
{
    use HasApiTokens, InviteTrait, SoftDeletes;

    protected $fillable = ['name', 'email','password','school_id', 'roles'];
    protected $hidden = ['password', 'remember_token', 'deleted_at'];

    public function school() {
        return $this->hasOne(School::class, 'id', 'school_id');
    }
}
