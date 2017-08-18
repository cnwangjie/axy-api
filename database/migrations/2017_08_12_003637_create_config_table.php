<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->string('key', 127)->comment('配置字段名');
            $table->string('value', 127)->comment('配置值');

            $table->primary('key');
        });

        DB::table('config')->insert([
            ['key' => 'allow_order', 'value' => 'true'],
            ['key' => 'reserve_ahead_days', 'value' => '2'],
            ['key' => 'reserve_ahead_secs', 'value' => '7200'],
            ['key' => 'pay_timeout_secs', 'value' => '600'],
            ['key' => 'auth_code_timeout_secs', 'value' => '1800'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config');
    }
}
