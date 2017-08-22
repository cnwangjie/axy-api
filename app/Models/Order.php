<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    protected $table = 'orders';

    protected $appends = ['detail'];

    protected $hidden = ['id', 'ip'];

    const INIT = 0                  // 初始状态
        , WAITING_FOR_PAY = 1       // 等待支付
        , INTERNAL_ERROR = 41       // 请求远程api错误
        , PAID = 3                  // 已支付成功
        , CANCELED_BY_CUSTEMER = 42 // 顾客取消
        , CANCELED_BY_ADMIN = 43    // 管理员取消
        , PAY_TIMEOUT = 44          // 支付超时
        , COMPLETED = 20;           // 订单完成

    public function detail()
    {
        return $this->belongsToMany(Dishes::class, 'order_details', 'order_id', 'item_id', 'id')
          ->using(OrderDetail::class)->withPivot(['price', 'sum']);
    }

    public function getDetailAttribute()
    {
        return $this->detail()->get();
    }

    public function isTimeout()
    {
        if ($this->status === self::WAITING_FOR_PAY) {
            $config = new Config;
            return $this->created_at->addSecond($config->pay_timeout_secs)->lessThan(Carbon::now());
        }
        return false;
    }
}
