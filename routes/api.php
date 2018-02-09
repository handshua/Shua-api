<?php


/** @var \Dingo\Api\Routing\Router $api */

use Dingo\Api\Routing\Router;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers'], function (Router $api) {
    $api->get('/', function () {
        return app()->version();
    });

    $api->post('/login', 'AuthController@login');
    $api->post('/register', 'AuthController@register');

    $api->get('/category', 'CategoryController@all');

    $api->get('/product', 'ProductController@all');
    $api->post('/product/{product}/buy', 'ProductController@buy');

    $api->get('/order/{order}', 'OrderController@show');
    $api->match(['get', 'post'], '/order/{order}/pay', 'OrderController@pay');

    $api->get('/payment','PaymentController@methods');
    $api->post('/payment/{payment}/notify', ['as' => 'payment.notify' ,'uses' => 'PaymentController@notify']);
});
