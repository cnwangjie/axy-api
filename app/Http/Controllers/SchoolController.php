<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use App\Models\School;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @apiDefine school 学校
 */

class SchoolController extends Controller
{

    /**
     * @api {get} /api/school 所有学校
     * @apiVersion 0.0.1
     * @apiGroup school
     *
     * @apiSuccess {Object[]} schools 学校数组
     * @apiSuccess {Number} schools.id 学校id
     * @apiSuccess {String} schools.name 学校名称
     * @apiSuccess {String} schools.created_at 创建时间
     * @apiSuccess {String} schools.updated_at 修改时间
     *
     */
    public function all()
    {
        return School::all();
    }


    /**
     * @api {get} /api/school/:id/canteen 某学校的所有餐厅
     * @apiVersion 0.0.1
     * @apiGroup school
     * @apiParam {Number} id 学校id
     *
     * @apiSuccess {Object[]} shops 商家数组
     * @apiSuccess {Number} id 商家id
     * @apiSuccess {Number} user_id 商家用户id
     * @apiSuccess {String} name 商家名称
     * @apiSuccess {Number} status 商家状态
     * @apiSuccess {Number} canteen_id 商家所在餐厅id
     * @apiSuccess {Number} floor 商家所在餐厅楼层
     * @apiSuccess {String} created_at 创建时间
     * @apiSuccess {String} updated_at 修改时间
     *
     */
    public function canteen(Request $request)
    {
        return Canteen::where('school', $request->id)->get();
    }
}
