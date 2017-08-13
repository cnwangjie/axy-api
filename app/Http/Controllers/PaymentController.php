<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * 需求 detail, js_code
     * 成功返回 object
     * 失败返回 error
     */
    public function orders()
    {
        // 用户身份认证
        // 参数认证
        $detail = json_decode($request->detail);
        // 验证订单商品状态 (有效性验证)
        // 生成订单 init
        // 调用 https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxloginobject换取openid

        // 统一支付接口 https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_1&index=1#
        // if success 订单waiting for payment 加密签名 -> 返回
        // else 订单intenal_error
        return;
    }


    // https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_1&index=1#
    public function forwx()
    {
        // 验证订单信息 订单paid

    }

    // 需求 by (custemer | shop | admin)
    public function cancelOrder()
    {
        // custemer
        // 验证之前状态 waiting for payment -> 直接 cancel && 调用关闭订单
        //             paid -> 验证时间在配送之前 -> cancel && 退款
        //                                 否则 -> 取消失败
        // order cancel 订单canceled by custemer

        // shop

        // admin
        // 之前状态 waiting for payment -> cancel && 关闭订单
        //              paid -> cancel && 退款
    }

    // ！！！调度器使用！！！
    public function paymentTimeout()
    {
        // 扫描waiting for payment 如果超时则关闭
        // 订单 timeout && 调用关闭订单
    }

    public function completeOrder()
    {
        // order complete 订单completed
    }
}
