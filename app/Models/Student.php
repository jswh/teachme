<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['name', 'username', 'password', 'school_id'];
    protected $hidden = ['password'];

    public function findForPassport($username) {
        return self::where('username', $username)->first();
    }

}
