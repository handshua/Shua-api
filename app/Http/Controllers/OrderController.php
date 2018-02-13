<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Transformers\OrderTransformer;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function all($page_number = false)
    {
        if ($page_number){
            return $this->response->paginator(Order::paginate($page_number),new OrderTransformer());
        }
        return $this->response->collection(Order::all(), new OrderTransformer);
    }

    public function show($order)
    {
        $order = Order::findOrFail($order);
        return $this->response->item($order, new OrderTransformer);
    }

    public function pay($order, $payment, Request $request)
    {
        $order = Order::findOrFail($order);
        $payment = Payment::findOrFail($payment);

        $notify_url = route('payment.notify', ['payment' => $payment->id]);
        $return_url = url('/');
        $redirect_url = $payment->getDriver()
            ->submit($order->id, $order->price / 100, $notify_url, $return_url);
        return compact('redirect_url');
    }
}
