<?php

namespace App\Events;

use App\Models\Order;

class OrderCreated extends Event
{
    protected $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
