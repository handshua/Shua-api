<?php

namespace App\Payment\Drivers;


use App\Payment\Contracts\PaymentDriver;
use Illuminate\Http\Request;

class EasyPay implements PaymentDriver
{
    protected static $required_params = [
        'base_url' => '支付平台网址',
        'pid' => '商户ID',
        'key' => '商户KEY',
        'type' => '支付方式',
        'product_name' => '商品名称',
    ];

    protected $params;

    static function getRequiredParams(): array
    {
        return self::$required_params;
    }

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * 提交支付
     *
     * @param $order_id
     * @param $price
     * @return string 支付页面网址
     */
    public function submit($order_id, $money, $notify_url, $return_url)
    {
        // 填充参数
        $params = [
            'pid' => $this->pid,
            'type' => $this->type,
            'out_trade_no' => $order_id,
            'notify_url' => $notify_url,
            'return_url' => $return_url,
            'name' => $this->product_name,
            'money' => $money
        ];

        $sign = $this->sign($params);
        $params['sign_type'] = 'MD5';
        $params['sign'] = $sign;

        $redirect_url = $this->base_url . '/submit.php?' . $this->createLinkstring($params);

        return $redirect_url;
    }

    /**
     * 异步通知校验
     *
     * @param Request $request
     * @return string|bool 返回订单号
     */
    public function validate(Request $request)
    {
        $data = $request->all();
        if (empty($data['sign']) || empty($data['trade_status']) || empty($data['out_trade_no']) || empty($data['sign_type']) || strtoupper($data['sign_type']) !== 'MD5')
            return false;
        if (strpos(strtolower($data['trade_status']), 'SUCCESS') === false)
            return false;

        $sign = $this->sign($data);
        return $sign === $this->sign($data) ? $data['out_trade_no'] : false;
    }


    public function __get($name)
    {
        return $this->params[$name];
    }


    protected function sign(Array $data)
    {
        // 排除 sign & sign_type
        unset($data['sign'], $data['sign_type']);

        // 排序
        ksort($data);

        // 拼接成 key1=var2&key2=var2 的形式
        $sign_string = $this->createLinkstring($data);

        return md5($sign_string);
    }

    protected function createLinkstring(Array $data)
    {
        $params = [];
        foreach ($data as $key => $value) {
            if (empty($key) || empty($value))
                continue;
            $params[] = "{$key}={$value}";
        }

        $link = implode('&', $params);
        return $link;
    }
}