<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use App\Transformers\PaymentTransformer;
use Illuminate\Http\Request;

class PaymentController extends Controller
{


    public function all()
    {
        return $this->response->collection(Payment::all(), new PaymentTransformer());
    }

    public function store(Request $request, $payment = null)
    {
        $drivers = app('payment')->getDrivers();
        $data = $this->validate($request, [
            'name' => 'required|string',
            'driver' => 'required|in:' . implode(",", array_keys($drivers)),
            'params' => 'required|array'
        ]);

        $payment = Payment::findOrNew($payment);

        if ($payment->fill($data)->save())
            return $this->response->created();
        else
            return $this->response->errorInternal();
    }

    public function notify(Request $request, $payment)
    {
        $payment = Payment::findOrFail($payment);
        $driver = app('payment')->driver($payment->driver);
        $result = $driver::validate($request, $payment->params['key']);
        if (!$result) {
            return 'FAIL';
        }

        /** @var Order $order */
        $order = Order::findOrFail($result);
        if ($order->status === Order::ORDER_STATUS_UNPAID) {
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
