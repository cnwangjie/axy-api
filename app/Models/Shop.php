<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    const NORMAL = 0
        , CLOSED = 1
        , INACTIVE = 2;

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function canteen()
    {
        return $this->belongsTo(Canteen::class, 'canteen_id', 'id');
    }

    public function supply()
    {
        return $this->canteen()->get()->first()->supply();
    }
}
