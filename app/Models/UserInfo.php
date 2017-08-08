<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $table = 'user_infos';

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
