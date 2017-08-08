<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->increments('id')->comment('菜品id');
            $table->string('name', 30)->comment('菜品名称');
            $table->string('description', 255)->nullable()->comment('菜品介绍');
            $table->string('img', 255)->nullable()->comment('展示图片的资源地址');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态 0:正常供应 1:停止供应 2:不显示');
            $table->integer('provider')->unsigned()->comment('供应者餐厅id');
            $table->integer('price')->unsigned()->comment('价格 (单位人民币分)');
            $table->timestamps();

            $table->index('provider');
            $table->foreign('provider')->references('id')->on('shops');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dishes');
    }
}
