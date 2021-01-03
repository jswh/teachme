<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = ['name', 'description', 'creator_id'];

    public function creator() {
        return $this->hasOne(Teacher::class, 'id', 'creator_id');
    }
}
