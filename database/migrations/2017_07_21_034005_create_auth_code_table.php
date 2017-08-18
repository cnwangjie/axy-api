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
            $table->string('tel', 15)->comment('手机号');
            $table->tinyInteger('type')->unsigned()->comment('验证方式 0:手机短信');
            $table->tinyInteger('usage')->unsigned()->comment('作用 0:注册 1:修改手机号 2:修改密码 3:登陆');
            $table->char('code', 6)->comment('六位随机数字');
            $table->tinyInteger('is_used')->unsigned()->comment('是否被使用 0:未被使用 1:已使用 3:过期');
            $table->timestamps();
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
