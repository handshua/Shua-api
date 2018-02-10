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

    $api->get('/order/{order}', 'OrderController@show');
    $api->match(['get', 'post'], '/order/{order}/pay', 'OrderController@pay');

    $api->get('/payment', 'PaymentController@methods');
    $api->post('/payment/{driver}/notify', ['as' => 'payment.notify', 'uses' => 'PaymentController@notify']);

    $api->group(['middleware' => 'jwt.auth'], function (Router $api) {

    });
});
