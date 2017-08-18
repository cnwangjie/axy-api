<?php

namespace App\Console\Commands;

use App\Models\AuthCode;
use App\Models\Config;
use Illuminate\Console\Command;

class CheckAuthCodeTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查验证码是否过期';

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
        AuthCode::where('is_used', AuthCode::UNUSED)->get()->each(function ($item, $key) {
            if ($item->isTimeout()) {
                $item->status = AuthCode::TIMEOUT;
                $item->save();
                $this->line("创建于 {$item->created_at->diffForHumans()} 的验证码 <info>{$item->code}</info> 超时");
            }
        });
    }
}
