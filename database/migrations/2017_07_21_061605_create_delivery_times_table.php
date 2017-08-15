<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_times', function (Blueprint $table) {
            $table->increments('id')->comment('配送时间id');
            $table->string('date', 30)->comment('配送日期YYYY/MM/DD (本地化时间GMT+8');
            $table->string('time', 30)->comment('配送时间 格式HH:mm-HH:mm (本地化时间GMT+8');
            $table->string('expire', 15)->comment('预定截止时间 格式HH:mm-HH:mm (本地化时间GMT+8');
            $table->tinyInteger('status')->unsigned()->comment('状态 0:可用 1:过期 2:未开启 3:禁用');
            $table->timestamps();
        });

        Schema::create('delivery_time_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('time', 30)->comment('配送时间');
            $table->string('expire', 15)->comment('预定截止时间');
        });

        DB::table('delivery_time_plan')->insert([
            ['time' => '12:10-12:50', 'expire' => '11:30'],
            ['time' => '17:45-18:25', 'expire' => '17:15'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_times');
        Schema::dropIfExists('delivery_time_plan');
    }
}
