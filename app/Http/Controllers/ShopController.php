<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Dishes;
use JWTAuth;
use Carbon\Carbon;
use Log;
use Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @apiDefine shop 商家
 */

class ShopController extends Controller
{

    /**
     * @api {get} /api/shop/:id 商家信息
     * @apiVersion 0.0.1
     * @apiGroup shop
     * @apiParam {Number} id 商家id
     *
     * @apiSuccess {Number} id 商家id
     * @apiSuccess {Number} user_id 用户id
     * @apiSuccess {String} name 商家名称
     * @apiSuccess {Number} status 商家状态
     * @apiSuccess {Number} canteen_id 商家所在餐厅id
     * @apiSuccess {Number} floor 商家所在餐厅楼层
     * @apiSuccess {String} created_at 创建时间
     * @apiSuccess {String} updated_at 修改时间
     *
     */
    public function index(Request $request)
    {
        $shop = Shop::find($request->id);
        if (!isset($shop)) throw new HttpException(404, 'SHOP_NOT_EXISTS');
        return $shop;
    }

    /**
     * @api {get} /api/shop/:id/dishes 商家菜品列表
     * @apiVersion 0.0.1
     * @apiGroup shop
     * @apiParam {Number} id 商家id
     *
     * @apiSuccess {Object[]} dishes 菜品列表
     * @apiSuccess {Number} dishes.id 菜品id
     * @apiSuccess {String} dishes.name 名称
     * @apiSuccess {String} dishes.description 介绍
     * @apiSuccess {String} dishes.img 图片地址
     * @apiSuccess {Number} dishes.status 状态
     * @apiSuccess {Number} dishes.provider 供应商家id
     * @apiSuccess {Number} dishes.price 价格(单位人名币分)
     * @apiSuccess {String} dishes.created_at 创建时间
     * @apiSuccess {String} dishes.updated_at 修改时间
     *
     */
    public function dishes(Request $request)
    {
        $shop = Shop::find($request->id);
        if (!isset($shop)) throw new HttpException(404, 'SHOP_NOT_EXISTS');
        return Dishes::where('provider', $request->id)->get();
    }

    /**
     * @api {get} /api/shop/:id/orders 商家订单
     * @apiVersion 0.0.1
     * @apiGroup shop
     * @apiHeader Authorization JWT token
     * @apiParam {Number} id 商家id
     * @apiParam {String} [since=0] 在此之后的订单 格式为 ISO 8601 时间戳: YYYY-MM-DDTHH:MM:SSZ.
     * @apiParam {String} [until=now] 在此之前的订单 格式为 ISO 8601 时间戳: YYYY-MM-DDTHH:MM:SSZ.
     *
     * @apiSuccess {Object[]} orders 订单
     *
     */
    public function orders(Request $request)
    {
        $id = $request->id;
        $shop = JWTAuth::parseToken()->authenticate()->id;
        if (Shop::where('user_id', $shop)->value('id') != $id) {
            throw new HttpException(401, 'NOT_ALLOWED');
        }
        $since = $request->since;
        $until = $request->until;

        $orders = Order::where('provider', $id);

        if (isset($since)) {
            try {
                $since = Carbon::parse($since);
                $orders = $orders->where('created_at', '>', $since);
            } catch (Exception $e) {
                throw new HttpException(400, 'TIME_FORMAT_ERROR');
            }
        }

        if (isset($until)) {
            try {
                $until = Carbon::parse($until);
                $orders = $orders->where('created_at', '<', $until);
            } catch (Exception $e) {
                throw new HttpException(400, 'TIME_FORMAT_ERROR');
            }
        }

        $orders = $orders->get();
        return $orders;
    }

    /**
     * @api {post} /api/shop/:id/dishes/add 添加菜品
     * @apiVersion 0.0.1
     * @apiGroup shop
     * @apiHeader Authorization JWT token
     * @apiParam {Number} id 商家id
     * @apiParam {String} name 名称
     * @apiParam {String} [description] 描述
     * @apiParam {String} [img] 图片url
     * @apiParam {Number} price 价格 (单位人名币分)
     *
     * @apiSuccess {Number} id 菜品id
     * @apiSuccess {String} name 名称
     * @apiSuccess {String} description 介绍
     * @apiSuccess {String} img 图片地址
     * @apiSuccess {Number} status 状态
     * @apiSuccess {Number} provider 供应商家id
     * @apiSuccess {Number} price 价格(单位人名币分)
     * @apiSuccess {String} created_at 创建时间
     * @apiSuccess {String} updated_at 修改时间
     *
     */
    public function addDishes(Request $request)
    {
        $id = $request->id;
        $shop = JWTAuth::parseToken()->authenticate()->id;
        if (Shop::where('user_id', $shop)->value('id') != $id) {
            throw new HttpException(401, 'NOT_ALLOWED');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:30',
            'description' => 'string|max:255',
            'img' => 'string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $dishes = new Dishes;
        $dishes->name = $request->name;
        $dishes->description = $request->description;
        $dishes->img = $request->img;
        $dishes->price = $request->price;
        $dishes->provider = $request->id;
        $dishes->status = 0;
        $dishes->save();
        return $dishes;
    }

    /**
    * @api {post} /api/shop/:id/status/set 设置商家状态
    * @apiVersion 0.0.1
    * @apiGroup shop
    * @apiHeader Authorization JWT token
    * @apiParam {Number} id 商家id
    * @apiParam {Number} status 新的状态
    *
    * @apiSuccess {String} status 请求状态
    * @apiSuccess {Number} current_status 商家当前状态
    */
    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $tokenUserId = JWTAuth::parseToken()->authenticate()->id;
        $shop = Shop::where('user_id', $tokenUserId)->first();
        if ($shop->id != $id) {
            throw new HttpException(401, 'NOT_ALLOWED');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $shop->status = $request->status;
        $shop->save();
        return response()->json([
            'status' => 'success',
            'current_status' => $shop->status,
        ], 200);
    }
}
