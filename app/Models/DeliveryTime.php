<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryTime extends Model
{
    const AVAILABLE = 0
        , EXPIRED = 1
        , NOT_START = 2
        , DISABLED = 3;

    protected $table = 'delivery_times';
}
