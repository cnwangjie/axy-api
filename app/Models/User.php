<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{    
    protected $hidden = ['password'];

    public function info()
    {
        return $this->hasOne(UserInfo::class, 'id', 'id');
    }
}
