<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address';

    protected $hidden = ['id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'id', 'custemer_id');
    }
}
