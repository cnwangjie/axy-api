<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderDetail extends Pivot
{
    protected $table = 'order_details';

    public $timestamps = false;
}
