<?php

namespace App\Transformers;


use App\Models\Payment;
use League\Fractal\TransformerAbstract;

class PaymentTransformer extends TransformerAbstract
{
    public function transform(Payment $payment)
    {
        return [
            'id' => $payment->id,
            'name' => $payment->name
        ];
    }
}