<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function all()
    {
        return Order::all();
    }

    public function show(Order $order)
    {
        if (!$order->exists) {
            $this->response->errorNotFound('订单不存在');
            return;
        }
        return $order;
    }

    public function pay(Order $order, Payment $payment, Request $request)
    {
        $notify_url = route('payment.notify', ['driver' => $payment->driver]);
        $return_url = url('/');
        $redirect_url = $payment->getDriver()
                                ->submit($order->id, $order->price / 100, $notify_url, $return_url);
        if ($request->isMethod('get'))
            return redirect($redirect_url);
        else
            return compact($redirect_url);
    }
}
