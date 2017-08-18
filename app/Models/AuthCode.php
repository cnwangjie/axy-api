<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthCode extends Model
{
    const UNUSED = 0
        , USED = 1
        , TIMEOUT = 2

        , SMS = 0

        , REGISTER = 0
        , CHANGE_TEL = 1
        , CHANGE_PASSWORD = 2
        , LOGIN = 3;

    public function isTimeout()
    {
        if ($this->status === self::UNUSED) {
            $config = new Config;
            return $this->created_at->addSecond($config->auth_code_timeout_secs)->lessThan(Carbon::now());
        }
        return false;
    }
}
