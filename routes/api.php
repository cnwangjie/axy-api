<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/shop/register', 'AuthController@shopRegister');
Route::post('auth/shop/login', 'AuthController@shopLogin');

Route::group([
    'prefix' => 'api',
], function () {
    Route::get('shop/{id}', 'ShopController@index');
    Route::get('shop/{id}/dishes', 'ShopController@dishes');

    Route::get('canteen', 'CanteenController@all');
    Route::get('canteen/{id}', 'CanteenController@index');
    Route::get('canteen/{id}/shop', 'CanteenController@shop');

    Route::get('school', 'SchoolController@all');

    Route::group([
        'middleware' => 'jwt.auth',
        'providers' => 'jwt'
    ], function () {
        Route::get('shop/{id}/orders', 'ShopController@orders');
        Route::post('shop/{id}/dishes/add', 'ShopController@addDishes');
        Route::post('shop/{id}/status/set', 'ShopController@changeStatus');
        Route::get('shop/{id}/amount', 'ShopController@sellAmount');

        Route::post('dishes/{id}/set', 'DishesController@change');
        Route::post('dishes/{id}/status/set', 'DishesController@changeStatus');
    });
});