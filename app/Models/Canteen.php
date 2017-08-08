<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Canteen extends Model
{
    protected $table = 'canteen';

    public function shop()
    {
        return $this->hasMany(Shop::class, 'canteen_id', 'id');
    }

    public function supply()
    {
        return $this->belongsToMany(Apartment::class, 'supply_relationship', 'canteen_id', 'apartment_id');
    }
}
