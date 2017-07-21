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
            $table->timestamps();
        });
        
        Schema::create('user_infos', function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('用户id');
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
        Schema::drop('users');
        Schema::drop('user_infos');
        Schema::drop('wx_users');
    }
}
