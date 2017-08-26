<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Custemer extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'custemer_id', 'id');
    }
}
