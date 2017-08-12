<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Order;

class CheckOrderTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查是否有订单支付超时';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Carbon::setLocale('zh');
        Order::where('status', Order::WAITING_FOR_PAY)->get()->each(function ($item) {
            if ($item->isTimeout()) {
                $item->status = Order::PAY_TIMEOUT;
                $item->save();
                $this->line("创建于 {$item->created_at->diffForHumans()} 的订单 <info>{$item->code}</info> 支付超时");
                // 向微信请求关闭订单
            }
        });
    }
}
