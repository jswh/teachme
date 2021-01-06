<?php

namespace App\Models;

use App\Models\Traits\CanChat;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use CanChat;

    protected $fillable = ['name', 'username', 'password', 'school_id'];
    protected $hidden = ['password'];
    protected $appends = ['chat_id'];

    public function findForPassport($username) {
        return self::where('username', $username)->first();
    }

    public function school() {
        return $this->hasOne(School::class, 'id', 'school_id');
    }

    protected function getChatIdPrefix() {
        return 's_';
    }

}
