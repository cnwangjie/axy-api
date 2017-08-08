<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopUser extends Model
{
    public function shop()
    {
        return $this->hasOne(Shop::class, 'id', 'id');
    }
}
