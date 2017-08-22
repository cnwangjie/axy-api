<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Order;
use JWTAuth;

/**
 * @apiDefine order 订单
 */

class OrderController extends Controller
{
    /**
     * @api {get} /api/order/:code 单个订单详情
     * @apiVersion 0.0.1
     * @apiGroup order
     * @apiHeader Authorization JWT token
     * @apiParam {String} code 订单编号
     *
     * @apiSuccess {String} code 订单编号
     * @apiSuccess {Number} price 订单金额 (单位人民币分)
     * @apiSuccess {Number} user_id 用户id
     * @apiSuccess {String} address 配送地址
     * @apiSuccess {Number} provider 供应者 商家id
     * @apiSuccess {Number} status 订单状态
     * @apiSuccess {String} delivery_date 配送日期
     * @apiSuccess {String} delivery_time 配送时间
     * @apiSuccess {String} remark 	备注
     * @apiSuccess {String} created_at 创建时间
     * @apiSuccess {String} updated_at 修改时间
     *
     */
    public function indexByCode(Request $request)
    {
        $order = Order::where('code', $request->code)->first();

        if (!isset($order)) {
            throw new HttpException(404, 'ORDER_NOT_EXISTS');
        }

        $tokenId = $tokenId = JWTAuth::parseToken()->authenticate()->id;

        $shopId = Shop::where('user_id', $tokenId)->first()->id;

        if ($order->user_id != $tokenId && $order->provider != $shopId) {
            throw new HttpException(403, 'NOT_ALLOWED');
        }

        return response()->json($order->toArray(), 200);
    }
}
