<?php

namespace App\Creators;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;

class OrderCreator
{
    public function create(Product $product, $number, array $params)
    {
        //TODO: 防止重复提交

        $this->validateParams($product, $params);
        /** @var Order $order */
        $order = new Order();
        $order->id = $this->generateId();
        $order->number = $number;
        $order->price = $product->price * $number;
        $order->status = Order::ORDER_STATUS_UNPAID;
        $order->product_id = $product->id;

        //TODO: 生成订单超时时间
        $order->expire_at = strtotime($product->warranty_period);

        if (!$order->save()) {
            return false;
        }

        if (!$this->insertParams($order, $params)) {
            return false;
        }

        return $order;
    }

    public function validateParams(Product $product, array $params)
    {
        if (count(array_diff_key($product->required_params, $params)) > 0) {
            throw new ValidationHttpException(['required_params' => 'order.required_params.error']);
        }
    }

    public function insertParams(Order $order, array $params)
    {
        /** 为了减少数据库写入次数 这里不用 ORM */
        // 要插入的数据集合
        $insert_params = [];
        foreach ($params as $key => $value) {
            // 单条数据
            $insert_param = [
                'key' => $key,
                'value' => $value,
                'order_id' => $order->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // 并入数据集合
            $insert_params[] = $insert_param;
        }

        // 写入数据库
        return DB::table('order_params')->insert($insert_params);
    }

    public function generateId()
    {
        return Uuid::generate();
    }
}
