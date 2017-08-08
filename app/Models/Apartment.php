<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $table = 'apartment';

    const MALE = 0
        ,FEMALE = 1
        ,MIXED = 2;

    public function beSupplied()
    {
        return $this->belongsToMany(Canteen::class, 'supply_relationship');
    }
}
