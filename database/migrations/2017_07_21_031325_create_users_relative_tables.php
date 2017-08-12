<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersRelativeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->comment('用户id');
            $table->string('tel', 15)->comment('手机号');
            $table->string('password', 100)->nullable()->comment('密码');
            $table->tinyInteger('role')->unsigned()->comment('身份 0:顾客 1:商家');
            $table->timestamps();
        });

        Schema::create('custemers', function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('用户id');
            $table->string('name', 10)->nullable()->comment('称呼');
            $table->tinyInteger('gender')->comment('性别 0:男 1:女');
            $table->string('sid', 30)->nullable()->comment('学号');
            $table->integer('school')->unsigned()->comment('学校id');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('school')->references('id')->on('schools')->onUpdate('cascade');
        });

        Schema::create('wx_users', function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('用户id');
            $table->string('wxid', 30)->comment('微信id');

            $table->foreign('id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->index('wxid');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custemers');
        Schema::dropIfExists('wx_users');
        Schema::dropIfExists('users');
    }
}
