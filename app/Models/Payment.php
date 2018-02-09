<?php

namespace App\Models;

use App\Payment\Contracts\PaymentDriver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Payment extends Model
{
    protected $fillable = ['name', 'driver'];


    public function getParamsAttribute()
    {
        return json_decode($this->params);
    }

    public function setParamsAttribute(Array $params){
        $this->params = json_encode($params);
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
