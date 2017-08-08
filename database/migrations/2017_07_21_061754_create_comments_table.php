<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id')->comment('评论id');
            $table->tinyInteger('score')->unsigned()->comment('评论值');
            $table->tinyInteger('type')->unsigned()->comment('评论类型');
            $table->string('content', 255)->nullable()->comment('评论内容');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->integer('shop_id')->unsigned()->comment('商家id');
            $table->string('reply', 255)->nullable()->comment('评论内容');
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
        Schema::dropIfExists('comments');
    }
}
