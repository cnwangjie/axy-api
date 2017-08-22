<?php

namespace App\Http\Controllers;

use App\Models\Custemer;
use Illuminate\Foundation\Testing\HttpException;
use Illuminate\Http\Request;

/**
 * @apiDefine custemer 顾客
 */

class CustemerController extends Controller
{
    /**
     * @api {get} /api/custemer/:id/orders 用户订单
     * @apiVersion 0.0.1
     * @apiGroup custemer
     * @apiHeader Authorization JWT token
     * @apiParam {Number} id 用户id
     * @apiParam {String} [since=0] 在此之后的订单 格式为 ISO 8601 时间戳: YYYY-MM-DDTHH:MM:SSZ.
     * @apiParam {String} [until=now] 在此之前的订单 格式为 ISO 8601 时间戳: YYYY-MM-DDTHH:MM:SSZ.
     *
     * @apiSuccess {Object[]} orders 订单
     *
     */
    public function orders(Request $request)
    {
        $id = $request->id;
        $tokenId = JWTAuth::parseToken()->authenticate()->id;
        if ($id != $tokenId) {
            throw new HttpException(401, 'NOT_ALLOWED');
        }

        $since = $request->since;
        $until = $request->until;

        $orders = Order::where('user_id', $id);

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
}
