<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressRelativeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment', function (Blueprint $table) {
            $table->increments('id')->comment('公寓楼id');
            $table->string('name', 30)->comment('公寓楼名称');
            $table->tinyInteger('type')->unsigned()->comment('0.男生公寓 1.女生公寓 2.混合公寓');
            $table->integer('school')->unsigned()->comment('学校id');
            $table->timestamps();

            $table->foreign('school')->references('id')->on('schools');
        });

        Schema::create('address', function (Blueprint $table) {
            $table->increments('id')->comment('地址id');
            $table->integer('custemer_id')->unsigned()->comment('顾客id');
            $table->integer('apartment')->unsigned()->comment('公寓楼id');
            $table->string('room', 30)->comment('房间号');
            $table->timestamps();

            $table->foreign('custemer_id')->references('id')->on('custemers');
            $table->foreign('apartment')->references('id')->on('apartment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address');
        Schema::dropIfExists('apartment');
    }
}
