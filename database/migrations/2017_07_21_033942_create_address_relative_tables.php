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
            $table->timestamps();
        });
        
        Schema::create('address', function (Blueprint $table) {
            $table->increments('id')->comment('地址id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->integer('apartment')->unsigned()->comment('公寓楼id');
            $table->string('room', 30)->comment('房间号');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::drop('address');
        Schema::drop('apartment');
    }
}
