<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryTimePlan extends Model
{
    protected $table = 'delivery_time_plan';

    public static function getTime()
    {
        return self::all()->map(function ($item) {
            return $item->time;
        })->toArray();
    }
}
