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
        'custemer_id' => Models\Custemer::all()->random()->id,
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
        'date' => date('Y/m/d'),
        'time' => date('H:i') . '-' . date('H:i', time() + 900),
        'status' => $faker->numberBetween(0, 3),
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
        'code' => strtoupper(substr($faker->md5, 0, 30)),
        'price' => 0,
        'user_id' => Models\User::all()->random()->id,
        'ip' => $faker->ipv4,
        'address' => $faker->address,
        'provider' => Models\Shop::all()->random()->id,
        'status' => $faker->randomElement([0, 1, 41, 3, 42, 43, 44, 20]),
        'delivery_date' => Models\DeliveryTime::all()->random()->date,
        'delivery_time' => Models\DeliveryTime::all()->random()->time,
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
        'user_id' => function() {
            return factory(Models\User::class)->create([
                'role' => 1,
            ])->id;
        },
        'name' => $faker->name,
        'status' => $faker->numberBetween(0, 2),
        'canteen_id' => Models\Canteen::all()->random()->id,
        'floor' => $faker->numberBetween(1, 3),
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
        'tel' => Faker\Factory::create('zh_CN')->phoneNumber,
        'password' => bcrypt('123456'),
    ];
});

$factory->define(Models\Custemer::class, function (Faker\Generator $faker) {
    return [
        'id' => function() {
           return factory(Models\User::class)->create([
               'role' => 0,
           ])->id;
        },
        'name' => Faker\Factory::create('zh_CN')->name,
        'gender' => $faker->numberBetween(0, 1),
        'sid' => $faker->numerify('########'),
        'school' => Models\School::all()->random()->id,
    ];
});
