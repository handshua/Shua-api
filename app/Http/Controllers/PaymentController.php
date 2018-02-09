<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Transformers\PaymentTransformer;

class PaymentController extends Controller
{
    public function methods()
    {
        return $this->response->collection(Payment::all(), new PaymentTransformer());
    }
}
