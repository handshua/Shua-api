<?php

namespace App\Payment\Contracts;


use Illuminate\Http\Request;

interface PaymentDriver
{
    static function getRequiredParams(): array;

    function __construct(Array $params);

    /**
     * 提交支付
     *
     * @param $order_id
     * @param $price
     * @return string 支付页面网址
     */
    function submit($order_id, $money, $notify_url, $return_url);

    /**
     * 异步通知校验
     *
     * @param Request $request
     * @return string|bool 返回订单号
     */
    static function validate(Request $request, $key);
}