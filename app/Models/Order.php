<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'price', 'number', 'expire_at'];

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
