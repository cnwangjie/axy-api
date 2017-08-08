<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public function shopUser()
    {
        return $this->belongsTo(ShopUser::class, 'id', 'id');
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
