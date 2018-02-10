<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use App\Transformers\PaymentTransformer;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function methods()
    {
        return $this->response->collection(Payment::all(), new PaymentTransformer());
    }

    public function notify($driver, Request $request)
    {
        $result = app('payment')->driver($driver)->validate($request);
        if (!$request) {
            return 'FAIL';
        }

        /** @var Order $order */
        $order = Order::findOrFail($result);
        if ($order->status !== Order::ORDER_STATUS_PAID) {
            $order->status = Order::ORDER_STATUS_PAID;

            //TODO: 邮件通知 && 生成质保到期时间
            if ($order->save())
                event(new OrderPaid($order));
            else
                return 'FAIL';
        }
        return "SUCCESS";

    }

}
