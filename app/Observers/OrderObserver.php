<?php

use App\Models\Order;

class OrderObserver
{
    public function deleting(Order $order)
    {
        $order->params()->delete();
    }

}