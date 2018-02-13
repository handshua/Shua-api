<?php

namespace App\Models;

use App\Payment\Contracts\PaymentDriver;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Payment extends Model
{
    protected $fillable = ['name', 'params', 'driver'];


    public function getParamsAttribute()
    {
        // 强制生成关联数组，不然会罢工
        return json_decode($this->attributes['params'], true);
    }

    public function setParamsAttribute($params)
    {
        if (is_array($params)) {
            $params = json_encode($params);
        }

        if ($params instanceof Jsonable) {
            $params = $params->toJson();
        }
        $this->attributes['params'] = $params;
    }

    /**
     * @return PaymentDriver
     */
    public function getDriver()
    {
        if ($this->exists() === false)
            throw new ModelNotFoundException('该支付方式不存在');

        return app('payment')->driver($this->driver, $this->params);
    }

}
