<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $appends = ['detail'];

    public function detail()
    {
        return $this->belongsToMany(Dishes::class, 'order_details', 'order_id', 'item_id', 'id')
          ->using(OrderDetail::class)->withPivot(['price', 'sum']);
    }

    public function getDetailAttribute()
    {
        return $this->detail()->get();
    }
}
