<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Custemer;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;

/**
 * @apiDefine custemer 顾客
 */

class CustemerController extends Controller
{
    /**
     * @api {get} /api/custemer/:id/orders 用户订单
     * @apiVersion 0.0.1
     * @apiGroup custemer
     * @apiPermission owner
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
        abort_if($id != $tokenId, 401, 'NOT_ALLOWED');

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

    /**
     * @api {get} /api/custemer/:id/address 获取用户地址
     * @apiVersion 0.0.1
     * @apiGroup custemer
     * @apiPermission owner
     * @apiHeader Authorization JWT token
     * @apiParam {Number} id 用户id
     *
     * @apiSuccess {Number} custemer_id 用户id
     * @apiSuccess {Number} apartment 公寓id
     * @apiSuccess {String} room 房间号
     * @apiSuccess {String} created_at 创建时间
     * @apiSuccess {String} updated_at 修改时间
     */
    public function getAddress(Request $request)
    {
        $id = $request->id;
        $tokenId = JWTAuth::parseToken()->authenticate()->id;
        abort_if($id != $tokenId, 401, 'NOT_ALLOWED');

        $address = Custemer::find($id)->address;
        abort_if(is_null($address), 404, 'ADDRESS_NOT_SET');

        return $address;
    }

    /**
     * @api {post} /api/custemer/:id/address 设置用户地址
     * @apiVersion 0.0.1
     * @apiGroup custemer
     * @apiPermission owner
     * @apiHeader Authorization JWT token
     * @apiParam {Number} id 用户id
     * @apiParam {Number} apartment 公寓id
     * @apiParam {String} room 房间号
     *
     * @apiParam {String} status 状态
     * @apiParam {Object} address 地址
     */
    public function setAddress(Request $request)
    {
        $id = $request->id;
        $tokenId = JWTAuth::parseToken()->authenticate()->id;
        abort_if($id != $tokenId, 401, 'NOT_ALLOWED');

        $validator = Validator::make($request->all(), [
            'apartment' => 'required|integer|exists:apartment,id',
            'room' => 'required|string|max:30',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');


        $custemer = Custemer::find($id);
        $address = $custemer->address;
        if (is_null($address)) {
            $address = new Address;
            $address->custemer_id = $id;
        }

        $address->apartment = $request->apartment;
        $address->room = $request->room;
        $address->save();

        return response()->json([
            'status' => 'success',
            'address' => $address->toArray(),
        ]);
    }
}
