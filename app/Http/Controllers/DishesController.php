<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Dishes;
use Validator;
use JWTAuth;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @apiDefine dishes 菜品
 */

class DishesController extends Controller
{


    /**
     * @api {get} /api/dishes/:id 获取菜品信息
     * @apiVersion 0.0.1
     * @apiGroup dishes
     * @apiParam {Number} id 菜品id
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
    public function index(Request $request)
    {
        return Dishes::find($request->id);
    }

    /**
     * @api {post} /api/dishes/:id/set 修改菜品信息
     * @apiVersion 0.0.1
     * @apiGroup dishes
     * @apiPermission owner
     * @apiHeader Authorization JWT token
     * @apiParam {Number} id 菜品id
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
    public function change(Request $request)
    {
        $dishesId = $request->id;
        $dishes = Dishes::find($dishesId);

        abort_if(!isset($dishes), 404, 'DISHES_NOT_EXISTS');

        $shop = JWTAuth::parseToken()->authenticate()->id;
        abort_if(Shop::where('user_id', $shop)->value('id') != $dishes->provider, 401, 'NOT_ALLOWED');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:30',
            'description' => 'string|max:255',
            'img' => 'string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $dishes->name = $request->name;
        if ($request->has('description'))
            $dishes->description = $request->description;
        if ($request->has('img'))
            $dishes->img = $request->img;
        $dishes->price = $request->price;
        $dishes->save();
        return $dishes;
    }

    /**
     * @api {post} /api/dishes/:id/status/set 修改菜品状态
     * @apiVersion 0.0.1
     * @apiGroup dishes
     * @apiPermission owner
     * @apiHeader Authorization JWT token
     * @apiParam {Number} id 菜品id
     * @apiParam {Number} status 新的状态
     *
     * @apiSuccess {String} status 请求状态
     * @apiSuccess {Number} current_status 菜品当前状态
     */
    public function changeStatus(Request $request)
    {
        $dishesId = $request->id;
        $dishes = Dishes::find($dishesId);

        abort_if(!isset($dishes), 404, 'DISHES_NOT_EXISTS');

        $shop = JWTAuth::parseToken()->authenticate()->id;
        abort_if(Shop::where('user_id', $shop)->value('id') != $dishes->provider, 401, 'NOT_ALLOWED');

        $validator = Validator::make($request->all(), [
            'status' => 'required|integer|in:0,1,2',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $dishes->status = $request->status;
        $dishes->save();
        return response()->json([
            'status' => 'success',
            'current_status' => $dishes->status,
        ], 200);
    }
}
