<?php

namespace App\Http\Controllers;

use App\Models\AuthCode;
use App\Models\Custemer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Shop;
use JWTAuth;
use JWTFactory;
use DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Yunpian\Sdk\Constant\Code;
use Yunpian\Sdk\YunpianClient;

/**
 *  @apiDefine auth 认证
 */

class AuthController extends Controller
{
    /**
     * @api {post} /auth/shop/register 商家注册
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {String} tel 手机号
     * @apiParam {String} password 密码
     * @apiParam {String} name 商家名称
     * @apiParam {Number} canteen_id 所属餐厅 id
     * @apiParam {Number} floor 餐厅所在楼层
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {Object} shop 商家信息
     * @apiSuccess {String} token JWTtoken
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
            throw new HttpException(403, 'USER_ALREADY_EXISTS');

        DB::beginTransaction();

        try {
            $user = new User;
            $user->tel = $request->tel;
            $user->password = bcrypt($request->password);
            $user->role = User::SHOP;
            $user->save();

            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->name;
            $shop->status = Shop::INACTIVE;
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
            'token' => JWTAuth::fromUser($user),
        ]);
    }

    /**
     * @api {post} /auth/shop/login 商家登陆
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {String} tel 手机号
     * @apiParam {String} password 密码
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {Object} shop 商家信息
     * @apiSuccess {String} token JWTtoken
     *
     */
    public function shopLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $tel = $request->tel;
        $password = $request->password;

        $user = User::where('tel', $tel)->where('role', 1)->first();

        if (!isset($user)) throw new HttpException(404, 'USER_NOT_EXISTS');

        $verified = Hash::check($password, $user->password);

        if (!$verified) throw new HttpException(403, 'PASSWORD_ERROR');

        $shop = Shop::where('user_id', $user->id)->first();

        return response()->json([
            'status' => 'success',
            'shop' => $shop->toArray(),
            'token' => JWTAuth::fromUser($user),
        ]);
    }

    /**
     * @api {post} /auth/password/change 修改密码
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {String} tel 手机号
     * @apiParam {String} password 新密码
     * @apiParam {String} code 验证码
     *
     * @apiParam {String} status 状态
     * @apiParam {String} tel 手机号
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'required|string',
            'password' => 'required|string',
            'code' => 'required|string',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $user = User::where('tel', $request->tel)->first();

        if (!isset($user)) {
            throw new HttpException(404, 'USER_NOT_EXISTS');
        }

        $authCode = AuthCode::where('tel', $request->tel)
            ->where('usage', AuthCode::CHANGE_PASSWORD)
            ->where('is_used', AuthCode::UNUSED)
            ->first();

        if (!isset($authCode)) {
            throw new HttpException(403, 'WRONG_AUTH_CODE');
        }

        $authCode->is_used = AuthCode::USED;
        $authCode->save();

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'tel' => $request->tel,
        ], 200);
    }

    /**
     * @api {get} /auth/sms 获取验证码短信
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {String} tel 手机号
     * @apiParam {Number} usage 用途
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {String} tel 手机号
     * @apiSuccess {String} usage 用途
     */
    public function getSMS(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'required|string',
            'usage' => 'required|integer|in:0,1,2,3',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $tel = $request->tel;
        $usage = $request->usage;

        if ($usage === AuthCode::REGISTER) {
            $user = User::where('tel', $tel)->get();
            if ($user) {
                throw new HttpException(403, 'TEL_IS_USED');
            }
        }

        $hasSend = AuthCode::where('tel', $tel)
            ->where('usage', $usage)
            ->where('is_used', AuthCode::UNUSED)
            ->first();

        if ($hasSend) {
            throw new HttpException(403, 'HAS_SEND');
        }

        $authCode = new AuthCode();
        $authCode->type = AuthCode::SMS;
        $authCode->usage = $usage;
        $authCode->code = str_pad(rand(0, 999999), 6, 0, STR_PAD_LEFT);
        $authCode->is_used = AuthCode::UNUSED;
        $authCode->tel = $tel;

        $yunpianClient = YunpianClient::create(env('YUNPIAN_APIKEY'));
        $r = $yunpianClient->sms()->single_send([
            YunpianClient::MOBILE => $tel,
            YunpianClient::TEXT => "【小最软件】您的验证码是{$authCode->code}",
        ]);

        if ($r->code() !== Code::OK) {
            throw new HttpException(500, 'SMS_SEND_ERROR');
        }

        $authCode->save();
        return response()->json([
            'status' => 'success',
            'tel' => $tel,
            'usage' => $usage,
        ], 200);
    }

    /**
     * @api {post} /auth/custemer/register 顾客注册
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {String} tel 手机号
     * @apiParam {String} password 密码
     * @apiParam {String} code 验证码
     * @apiParam {String} name 称呼
     * @apiParam {Number} gender 性别
     * @apiParam {Number} school 学校id
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {Object} custemer 用户对象
     * @apiSuccess {String} token JWTtoken
     */
    public function custemerRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'required|string',
            'password' => 'required|string',
            'code' => 'required|string',
            'name' => 'required|string|max:10',
            'gender' => 'required|integer|in:0,1',
            'school' => 'required|integer|exists:school,id',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $tel = $request->tel;

        $authCode = AuthCode::where('tel', $request->tel)
            ->where('code', $request->code)
            ->where('usage', AuthCode::REGISTER)
            ->where('is_used', AuthCode::UNUSED)
            ->first();

        if (!isset($authCode)) {
            throw new HttpException(403, 'WRONG_AUTH_CODE');
        }

        $authCode->is_used = AuthCode::USED;
        $authCode->save();

        DB::beginTransaction();

        try {
            $user = new User;
            $user->tel = $tel;
            $user->password = bcrypt($request->password);
            $user->role = User::CUSTEMER;
            $user->save();

            $custemer = new Custemer;
            $custemer->id = $user->id;
            $custemer->name = $request->name;
            $custemer->gender = $request->gender;
            $custemer->school = $request->school;
            $custemer->save();

            DB::commit();
        } catch (\Exception $exception) {

            DB::rollBack();
            return new HttpException(500, 'DATABASE_ERROR');
        }

        return response()->json([
            'status' => 'success',
            'custemer' => $custemer->toArray(),
            'token' => JWTAuth::fromUser($user),
        ], 200);
    }

    /**
     * @api {post} /auth/custemer/login/password 顾客登陆（使用密码）
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {String} tel 手机号
     * @apiParam {String} password 密码
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {Object} custemer 顾客对象
     * @apiSuccess {String} token JWTtoken
     */
    public function custemerPasswordLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $tel = $request->tel;
        $password = $request->password;

        $user = User::where('tel', $tel)->where('role', User::CUSTEMER)->first();

        if (!isset($user)) throw new HttpException(404, 'USER_NOT_EXISTS');

        $verified = Hash::check($password, $user->password);

        if (!$verified) throw new HttpException(403, 'PASSWORD_ERROR');

        $custemer = Custemer::where('id', $user->id)->first();

        return response()->json([
            'status' => 'success',
            'shop' => $custemer->toArray(),
            'token' => JWTAuth::fromUser($user),
        ]);
    }

    /**
     * @api {post} /auth/custemer/login/sms 顾客登陆（短信验证）
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiParam {String} tel 手机号
     * @apiParam {String} code 验证码
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {Object} custemer 顾客对象
     * @apiSuccess {String} token JWTtoken
     */
    public function custemerSMSLogim(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'required|string',
            'code' => 'required|string',
        ]);

        if ($validator->fails())
            throw new HttpException(400, 'BAD_REQUEST');

        $tel = $request->tel;
        $code = $request->code;

        $user = User::where('tel', $tel)->where('role', User::CUSTEMER)->first();

        if (!isset($user)) {
            throw new HttpException(403, 'USER_NOT_EXISTS');
        }

        $authCode = AuthCode::where('tel', $request->tel)
            ->where('code', $request->code)
            ->where('usage', AuthCode::LOGIN)
            ->where('is_used', AuthCode::UNUSED)
            ->first();

        if (!isset($authCode)) {
            throw new HttpException(403, 'WRONG_AUTH_CODE');
        }

        $authCode->is_used = AuthCode::USED;
        $authCode->save();

        $custemer = Custemer::where('id', $user->id)->first();

        return response()->json([
            'status' => 'success',
            'shop' => $custemer->toArray(),
            'token' => JWTAuth::fromUser($user),
        ]);
    }

    /**
     * @api {get} /auth/token/refresh 更新令牌
     * @apiVersion 0.0.1
     * @apiGroup auth
     * @apiHeader Authorization JWT token
     *
     * @apiSuccess {String} status 状态
     * @apiSuccess {String} token 新的token
     */
    public function refreshToken(Request $request)
    {
        $token = $request->attributes->get('token');

        return response()->json([
            'status' => 'success',
            'token' => $token,
        ], 200);
    }
}
