<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopRelativeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen', function (Blueprint $table) {
            $table->increments('id')->comment('餐厅id');
            $table->string('name', 30)->comment('餐厅名称');
            $table->timestamps();
        });
        
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id')->comment('商家id');
            $table->string('name', 30)->comment('商家名称');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('商家状态 0:正常供应 1:停止供应');
            $table->integer('canteen_id')->unsigned()->comment('商家所在餐厅id');
            $table->timestamps();
            
            $table->foreign('canteen_id')->references('id')->on('canteen');
        });
        
        Schema::create('supply_relationship', function (Blueprint $table) {
            $table->integer('canteen_id')->unsigned()->comment('餐厅id');
            $table->integer('apartment_id')->unsigned()->comment('公寓id');
            
            $table->foreign('canteen_id')->references('id')->on('canteen');
            $table->foreign('apartment_id')->references('id')->on('apartment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shops');
        Schema::drop('canteen');
        Schema::drop('supply_relationship');
    }
}
