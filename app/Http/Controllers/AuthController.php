<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Shop;
use JWTAuth;
use JWTFactory;
use DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 *  @apiDefine auth 认证
 */

class AuthController extends Controller
{
    /**
     * @api {post} /auth/shop/register 商家注册
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {string} tel 手机号
     * @apiParam {String} password 密码
     * @apiParam {String} name 商家名称
     * @apiParam {Number} canteen_id 所属餐厅 id
     * @apiParam {Number} floor 餐厅所在楼层
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {Object} shop 商家信息
     *
     */
    public function shopRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'required|string',
            'password' => 'required|string',
            'name' => 'required|string',
            'canteen_id' => 'required|integer|exists:canteen,id',
            'floor' => 'required|integer|max:10',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        if (User::where('tel', $request->tel)->first())
            throw new HttpException(200, 'USER_ALREADY_EXISTS');

        DB::beginTransaction();

        try {
            $user = new User;
            $user->tel = $request->tel;
            $user->password = bcrypt($request->password);
            $user->role = 1;
            $user->save();

            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->name;
            $shop->status = 2;
            $shop->floor = $request->floor;
            $shop->canteen_id = $request->canteen_id;
            $shop->save();

            DB::commit();
        } catch (\Exception $exception) {

            DB::rollBack();
            return new HttpException(500, 'DATABASE_ERROR');
        }

        return response()->json([
            'status' => 'success',
            'shop' => $shop->toArray(),
        ]);
    }

    /**
     * @api {post} /auth/shop/login 商家登陆
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {string} tel 手机号
     * @apiParam {String} password 密码
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {Object} shop 商家信息
     * @apiSuccess {String} token JWTtoken
     *
     */
    public function shopLogin(Request $request)
    {
        $tel = $request->tel;
        $password = $request->password;

        $user = User::where('tel', $tel)->where('role', 1)->first();

        if (!isset($user)) throw new HttpException(404, 'USER_NOT_EXISTS');

        $verified = Hash::check($password, $user->password);

        if (!$verified) throw new HttpException(200, 'PASSWORD_ERROR');

        $shop = Shop::where('user_id', $user->id)->first();

        return response()->json([
            'status' => 'success',
            'shop' => $shop->toArray(),
            'token' => JWTAuth::fromUser($user),
        ]);
    }
}
