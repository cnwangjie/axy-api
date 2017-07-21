<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_users', function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('商家id');
            $table->string('username', 50)->comment('用户名');
            $table->string('password', 50)->comment('密码');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态 0:未激活 1:激活');
            $table->timestamps();
            
            $table->primary('id');
            $table->foreign('id')->references('id')->on('shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shop_users');
    }
}
