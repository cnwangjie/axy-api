<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersRelativeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id')->comment('订单id');
            $table->string('code')->comment('订单编号');
            $table->integer('price')->unsigned()->comment('订单金额 (单位人民币分)');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->string('address', 255)->comment('配送地址 详细地址');
            $table->integer('provider')->unsigned()->comment('供应者 餐厅id');
            $table->tinyInteger('status')->unsigned()->comment('订单状态');
            $table->integer('delivery_time_id')->unsigned()->comment('配送时间id');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('delivery_time_id')->references('id')->on('delivery_times');
            $table->foreign('provider')->references('id')->on('shops');
            $table->index('provider');
            $table->index('user_id');
        });
        
        Schema::create('order_details', function (Blueprint $table) {
            $table->integer('order_id')->unsigned()->comment('订单id');
            $table->integer('item_id')->unsigned()->comment('菜品id');
            $table->integer('price')->unsigned()->comment('下单价格 (单位人民币分)');
            $table->integer('sum')->unsigned()->comment('数量');
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('item_id')->references('id')->on('dishes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
        Schema::drop('order_details');
    }
}
