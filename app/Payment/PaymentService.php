<?php

namespace App\Payment;

use App\Payment\Contracts\PaymentDriver;
use ReflectionClass;

class PaymentService
{
    protected $drivers = [];


    /**
     * @param $name
     * @param array $params
     * @return PaymentDriver
     * @throws \Exception
     */
    public function driver($name, Array $params = [])
    {
        if (!isset($this->drivers[$name])) {
            throw new \Exception("Driver '{$name}' does not exist");
        }

        $driver = new ReflectionClass($this->drivers[$name]);

        if ($driver->isInstantiable()) {
            $driver = $driver->newInstance($params);
        } else {
            throw new \Exception('Invalid driver');
        }

        if ($driver instanceof PaymentDriver) {
            throw new \Exception('Invalid driver');
        }

        return $driver;

    }

    public function getDriverRequiredParams($name)
    {
        if (!isset($this->drivers[$name])) {
            throw new \Exception("Driver '{$name}' does not exist");
        }

        return ($this->drivers[$name])::getRequiredParams;
    }

    /**
     * @return array
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

}