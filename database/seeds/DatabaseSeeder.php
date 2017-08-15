<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models;

class DatabaseSeeder extends Seeder
{

    protected $tables = [
        'address',
        'apartment',
        'auth_codes',
        'canteen',
        'comments',
        'delivery_times',
        'dishes',
        'orders',
        'order_details',
        'schools',
        'shops',
        'supply_relationship',
        'users',
        'custemers',
        'wx_users',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }

        factory(Models\School::class, 1)->create();
        factory(Models\Custemer::class, 10)->create();
        factory(Models\Apartment::class, 5)->create();
        factory(Models\Address::class, 15)->create();
        factory(Models\Canteen::class, 5)->create();
        factory(Models\Shop::class, 20)->create();
        factory(Models\Dishes::class, 200)->create();
        Artisan::call('task:time');
        factory(Models\SupplyRelationship::class, 20)->create();
        factory(Models\Order::class, 100)->create()->each(function ($order) {
            factory(Models\OrderDetail::class, 3)->create([
                'order_id' => $order->id,
            ])->each(function ($orderDetail) use ($order) {
                $order->price += $orderDetail->price * $orderDetail->sum;
            });
            $order->save();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $this->command->info('Test data inserted successfully!');
    }
}
