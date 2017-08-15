<?php

namespace App\Console\Commands;

use function foo\func;
use Illuminate\Console\Command;
use App\Models\DeliveryTime;
use App\Models\DeliveryTimePlan;
use App\Models\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateDeliveryTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成每天的配送时间选项';

    protected $config;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 检查过期
        DeliveryTime::where('status', 0)->get()->each(function ($item, $key) {
            $date = $item->date;
            $expireTime = $item->expire;
            $dt = Carbon::createFromFormat('Y/m/d H:i+', $date . ' ' . $time, 'Asia/Shanghai');
            if ($expireTime->lessThan(Carbon::now())) {
                $item->status = 1;
                $item->save();
                $this->line("<info>{$date} {$item->time}</info> 已经过期");
            }
        });

        // 生成计划
        $plans = DeliveryTimePlan::all();
        foreach (range(0, $this->config->reserve_ahead_days) as $daynum) {

            $date = Carbon::now()->addDay($daynum)->format('Y/m/d');

            $thisDateTables = DeliveryTime::where('date', $date)->get();

            foreach ($plans as $plan) {

                $curDateColumn = $thisDateTables->where('time', $plan->time)->first();

                if (!$curDateColumn) {
                    $newdt = new DeliveryTime;
                    $newdt->date = $date;
                    $newdt->time = $plan->time;
                    $newdt->expire = $plan->expire;
                    $newdt->status = 0;
                    $newdt->save();
                    $this->line("<info>{$date} {$plan->time}</info> 创建成功");
                } else if ($curDateColumn->status === DeliveryTime::NOT_START) {
                    $curDateColumn->status = DeliveryTime::AVAILABLE;
                    $curDateColumn->save();
                    $this->line("<info>{$date} {$plan->time}</info> 开启成功");
                } else {
                    $this->line("<info>{$date} {$plan->time}</info> 已经存在");
                }
            }
        }
    }
}
