<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }

        factory(Models\School::class, 1)->create();
        factory(Models\Canteen::class, 5)->create();
        factory(Models\Shop::class, 20)->create();
        factory(Models\Apartment::class, 5)->create()->each(function ($apartment) {
            $apartment->beSupplied()->attach(Models\Canteen::all()->random(3));
        });
        factory(Models\Custemer::class, 10)->create();
        Models\Custemer::all()->each(function ($custemer) {
            factory(Models\Address::class)->create([
                'custemer_id' => $custemer->id,
            ]);
        });
        factory(Models\Dishes::class, 200)->create();
        Artisan::call('task:time');
        factory(Models\AuthCode::class, 30)->create();
        factory(Models\Order::class, 100)->create()->each(function ($order) {
            factory(Models\OrderDetail::class, 3)->create([
                'order_id' => $order->id,
            ])->each(function ($orderDetail) use ($order) {
                $order->price += $orderDetail->price * $orderDetail->sum;
            });
            $order->save();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        Model::reguard();

        $this->command->info('Test data inserted successfully!');
    }
}
