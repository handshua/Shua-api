<?php


/** @var \Dingo\Api\Routing\Router $api */

/** @var \Laravel\Lumen\Routing\Router $router */

use Dingo\Api\Routing\Router;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers'], function (Router $api) {

    $api->get('/', function () {
        return app()->version();
    });

    $api->post('/login', 'AuthController@login');
    $api->post('/register', 'AuthController@register');

    $api->get('/category', 'CategoryController@all');
    $api->group(['middleware' => ['permission:manage categories']], function (Router $api) {
        $api->put('/category/{category?}', 'CategoryController@store');
        $api->delete('/category/{category}', 'CategoryController@delete');
    });


    $api->get('/product', 'ProductController@all');
    $api->post('/product/{product}/buy', 'ProductController@buy');
    $api->group(['middleware' => ['permission:manage products']], function (Router $api) {
        $api->put('/product/{product?}', 'ProductController@store');
        $api->delete('/product/{product}', 'ProductController@delete');
    });

    $api->match(['get', 'post'], '/order/{order}/pay/{payment}', 'OrderController@pay');
    $api->group(['middleware' => ['permission:manage orders']], function (Router $api) {
        $api->get('/order/list/{page_number?}', 'OrderController@all');
    });
    $api->get('/order/{order}', 'OrderController@show');


    $api->get('/payment', 'PaymentController@all');
    $api->group(['middleware' => ['permission:manage payment']], function (Router $api) {
        $api->put('/payment/{product?}', 'PaymentController@store');
    });

    $api->group(['middleware' => 'jwt.auth'], function (Router $api) {

    });
});

$router->get('/payment/{payment}/notify', ['as' => 'payment.notify', 'uses' => 'PaymentController@notify']);
$router->post('/payment/{payment}/notify', ['as' => 'payment.notify', 'uses' => 'PaymentController@notify']);

