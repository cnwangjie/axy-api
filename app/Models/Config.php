<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';

    private $config;

    public function __get($key)
    {
        if (!isset($this->config)) {
            $data = self::all()->toArray();
            $this->config = [];
            foreach ($data as $item) {
                $this->config[$item['key']] = $item['value'];
            }
        }

        if (isset($this->config[$key])) {
            $r = $this->config[$key];
            if ($r === 'false') return false;
            if ($r === 'true') return true;
            if (is_numeric($r)) return intval($r);
            return $r;
        }

        return parent::__get($key);
    }
}