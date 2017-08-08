<?php

use App\Models;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Models\Address::class, function (Faker\Generator $faker) {
    return [
        'user_id' => Models\User::all()->random()->id,
        'apartment' => Models\Apartment::all()->random()->id,
        'room' => $faker->numerify('####'),
    ];
});

$factory->define(Models\Apartment::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->streetName,
        'type' => $faker->numberBetween(0, 2),
        'school' => Models\School::all()->random()->id,
    ];
});

$factory->define(Models\Canteen::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'school' => Models\School::all()->random()->id,
    ];
});

$factory->define(Models\Comment::class, function (Faker\Generator $faker) {
    return [
        'score' => $faker->numberBetween(1, 5),
        'type' => $faker->numberBetween(0, 1),
        'content' => $faker->text(200),
        'user_id' => Models\User::all()->random()->id,
        'shop_id' => Models\Shop::all()->random()->id,
        'reply' => $faker->text(20),
    ];
});

$factory->define(Models\DeliveryTime::class, function (Faker\Generator $faker) {
    return [
        'time' => $faker->numerify('##:##-##:##'),
    ];
});

$factory->define(Models\Dishes::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->lastName,
        'description' => $faker->text(100),
        'img' => $faker->imageUrl(400, 400, 'food'),
        'status' => $faker->numberBetween(0, 2),
        'provider' => Models\Shop::all()->random()->id,
        'price' => $faker->numberBetween(100, 500),
    ];
});

$factory->define(Models\Order::class, function (Faker\Generator $faker) {
    return [
        'code' => $faker->numerify('##########'),
        'price' => 0,
        'user_id' => Models\User::all()->random()->id,
        'address' => $faker->address,
        'provider' => Models\Shop::all()->random()->id,
        'status' => $faker->numberBetween(1, 5),
        'delivery_time_id' => Models\DeliveryTime::all()->random()->id,
        'remark' => $faker->text(20),
    ];
});

$factory->define(Models\OrderDetail::class, function (Faker\Generator $faker) {
    return [
        'order_id' => Models\Order::all()->random()->id,
        'item_id' => Models\Dishes::all()->random()->id,
        'price' => $faker->numberBetween(100, 500),
        'sum' => $faker->numberBetween(1, 5),
    ];
});

$factory->define(Models\School::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->city,
    ];
});


$factory->define(Models\Shop::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'status' => $faker->numberBetween(0, 1),
        'canteen_id' => Models\Canteen::all()->random()->id,
    ];
});

$factory->define(Models\ShopUser::class, function (Faker\Generator $faker) {
    return [
        'id' => function() {
            return factory(Models\Shop::class)->create()->id;
        },
        'tel' => $faker->e164PhoneNumber,
        'password' => bcrypt('123456'),
        'status' => $faker->numberBetween(0, 1),
    ];
});


$factory->define(Models\SupplyRelationship::class, function (Faker\Generator $faker) {
    return [
        'canteen_id' => Models\Canteen::all()->random()->id,
        'apartment_id' => Models\Apartment::all()->random()->id,
    ];
});

$factory->define(Models\User::class, function (Faker\Generator $faker) {
    return [
        'tel' => $faker->e164PhoneNumber,
        'password' => bcrypt('password'),
    ];
});

$factory->define(Models\UserInfo::class, function (Faker\Generator $faker) {
    return [
       'id' => function() {
           return factory(Models\User::class)->create()->id;
       },
       'name' => Faker\Factory::create('zh_CN')->name,
       'sid' => $faker->numerify('########'),
       'school' => Models\School::all()->random()->id,
    ];
});
