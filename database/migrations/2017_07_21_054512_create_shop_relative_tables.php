<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopRelativeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen', function (Blueprint $table) {
            $table->increments('id')->comment('餐厅id');
            $table->string('name', 30)->comment('餐厅名称');
            $table->integer('school')->unsigned()->comment('学校id');
            $table->timestamps();

            $table->foreign('school')->references('id')->on('schools')->onUpdate('cascade');
        });

        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id')->comment('商家id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->string('name', 30)->comment('商家名称');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('商家状态 0:正常供应 1:停止供应 2:未激活');
            $table->integer('canteen_id')->unsigned()->comment('商家所在餐厅id');
            $table->tinyInteger('floor')->unsigned()->comment('商家所在餐厅楼层');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('canteen_id')->references('id')->on('canteen');
        });

        Schema::create('supply_relationship', function (Blueprint $table) {
            $table->integer('canteen_id')->unsigned()->comment('餐厅id');
            $table->integer('apartment_id')->unsigned()->comment('公寓id');

            $table->foreign('canteen_id')->references('id')->on('canteen');
            $table->foreign('apartment_id')->references('id')->on('apartment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supply_relationship');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('canteen');
    }
}
