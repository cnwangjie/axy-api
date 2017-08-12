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
        DeliveryTime::where('status', 0)->get()->each(function ($item, $key) {
            $date = $item->date;
            $time = $item->time;
            $dt = Carbon::createFromFormat('Y/m/d H:i+', $date . ' ' . $time, 'Asia/Shanghai');
            if ($dt->subSecond($this->config->reserve_ahead_secs)->lessThan(Carbon::now())) {
                $item->status = 1;
                $item->save();
                $this->line("<info>{$date} {$time}</info> 已经过期");
            }
        });

        $times = DeliveryTimePlan::getTime();

        foreach (range(0, $this->config->reserve_ahead_days) as $daynum) {
            $date = Carbon::now()->addDay($daynum)->format('Y/m/d');
            $dts = DeliveryTime::where('date', $date)->get();
            foreach ($times as $time) {
                $curdt = $dts->where('time', $time)->first();
                if (!$curdt) {
                    $newdt = new DeliveryTime;
                    $newdt->date = $date;
                    $newdt->time = $time;
                    $newdt->status = 0;
                    $newdt->save();
                    $this->line("<info>{$date} {$time}</info> 创建成功");
                } else if ($curdt->status === 2) {
                    $curdt->status = 0;
                    $curdt->save();
                    $this->line("<info>{$date} {$time}</info> 开启成功");
                } else {
                    $this->line("<info>{$date} {$time}</info> 已经存在");
                }
            }
        }
    }
}
