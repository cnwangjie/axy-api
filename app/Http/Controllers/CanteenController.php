<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use App\Models\Canteen;
use App\Models\Shop;

/**
 * @apiDefine canteen 餐厅
 */

class CanteenController extends Controller
{
    /**
     * @api {get} /api/canteen 所有餐厅
     * @apiVersion 0.0.1
     * @apiGroup canteen
     *
     * @apiSuccess {Object[]} canteens 餐厅数组
     * @apiSuccess {Number} canteens.id 餐厅id
     * @apiSuccess {String} canteens.name 餐厅名称`
     * @apiSuccess {Number} canteens.school 所在学校id
     * @apiSuccess {String} canteens.created_at 创建时间
     * @apiSuccess {String} canteens.updated_at 修改时间
     *
     */
    public function all()
    {
        return Canteen::all();
    }


    /**
     * @api {get} /api/canteen/:id 餐厅信息
     * @apiVersion 0.0.1
     * @apiGroup canteen
     * @apiParam {Number} id 餐厅id
     *
     * @apiSuccess {Number} id 餐厅id
     * @apiSuccess {String} name 餐厅名称
     * @apiSuccess {Number} school 所在学校id
     * @apiSuccess {String} created_at 创建时间
     * @apiSuccess {String} updated_at 修改时间
     *
     */
    public function index(Request $request)
    {
        return Canteen::find($request->id);
    }


    /**
     * @api {get} /api/canteen/:id/shop 某餐厅的所有商家
     * @apiVersion 0.0.1
     * @apiGroup canteen
     * @apiParam {Number} id 餐厅id
     *
     * @apiSuccess {Object[]} shops 商家数组
     * @apiSuccess {Number} id 商家id
     * @apiSuccess {Number} user_id 商家用户id
     * @apiSuccess {String} name 商家名称
     * @apiSuccess {String} img 图片地址
     * @apiSuccess {Number} status 商家状态
     * @apiSuccess {Number} canteen_id 商家所在餐厅id
     * @apiSuccess {Number} floor 商家所在餐厅楼层
     * @apiSuccess {String} created_at 创建时间
     * @apiSuccess {String} updated_at 修改时间
     *
     */
    public function shop(Request $request)
    {
        $canteen_id = $request->id;
        return Shop::where('canteen_id', $canteen_id)->get();
    }
}
