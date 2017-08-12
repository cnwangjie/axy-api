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
Route::get('shop/{id}', 'ShopController@index');
Route::get('shop/{id}/dishes', 'ShopController@dishes');

Route::group([
    'middleware' => 'jwt.auth',
    'providers' => 'jwt'
], function() {
    Route::get('shop/{id}/orders', 'ShopController@orders');
    Route::post('shop/{id}/dishes/add', 'ShopController@addDishes');
    Route::post('shop/{id}/status/set', 'ShopController@changeStatus');
    Route::post('dishes/{id}/set', 'DishesController@change');
    Route::post('dishes/{id}/status/set', 'DishesController@changeStatus');
});