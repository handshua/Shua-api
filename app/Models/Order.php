<?php

namespace App\Models;

use App\Events\OrderCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{

    const ORDER_STATUS_UNPAID = -1,
        ORDER_STATUS_PAID = 0,
        ORDER_STATUS_PROCESSING = 1,
        ORDER_STATUS_PROCESSED = 2;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'price', 'number', 'expire_at'];

    protected $dispatchesEvents = [
        'created' => OrderCreated::class,
    ];

    public function params()
    {
        return DB::table('order_params')
            ->where('order_id', '=', $this->id);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
