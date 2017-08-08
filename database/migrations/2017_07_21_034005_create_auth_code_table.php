<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_codes', function (Blueprint $table) {
            $table->increments('id')->comment('验证码id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->tinyInteger('type')->unsigned()->comment('验证方式 0:手机短信');
            $table->tinyInteger('usage')->unsigned()->comment('作用 0:注册 1:修改手机号');
            $table->char('code', 6)->comment('六位随机数字');
            $table->tinyInteger('is_used')->unsigned()->comment('是否被使用 0:未被使用 1:已使用');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_codes');
    }
}
