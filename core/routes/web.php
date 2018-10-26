<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/token', 'AuthController@signin');
    $router->post('/refresh', 'AuthController@refresh');
    $router->get('/confirmation/{token}', 'AuthController@confirmation');
});
$router->post('/signup', 'UserController@store');

$router->group(['prefix' => 'payments'], function () use ($router)
{
    $router->get('callback', 'PaymentController@callback');
    $router->get('check', 'PaymentController@check');
    $router->post('url', 'PaymentController@paymentUrl');
});

$router->group(['prefix' => 'videos'], function () use ($router)
{
    $router->get('watch', 'VideoController@play');
    $router->get('download', 'VideoController@download');

    $router->group(['middleware' => 'auth:api'], function ($router)
    {
        $router->get('files', 'VideoController@getFiles');
        $router->get('', 'VideoController@index');
        $router->post('', 'VideoController@store');

        $router->group(['prefix' => '{id}'], function () use ($router)
        {
            $router->get('', 'VideoController@show');
            $router->put('', 'VideoController@update');
            $router->delete('', 'VideoController@delete');
        });
    });
});

$router->group(['prefix' => 'movies'], function () use ($router)
{
    $router->get('watch', 'MovieController@show');

    $router->group(['middleware' => 'auth:api'], function ($router)
    {
        $router->get('right-access/{reference}', 'VideoController@rightAccess');
        $router->get('infos', 'VideoController@generate');
        $router->get('', 'MovieController@index');
        $router->post('', 'MovieController@store');

        $router->group(['prefix' => '{id}'], function () use ($router)
        {
            $router->get('', 'MovieController@show');
            $router->put('', 'MovieController@update');
            $router->delete('', 'MovieController@delete');
        });
    });
});

$router->group(['middleware' => 'auth:api'], function ($router)
{
    $router->get('me', 'AuthController@me');
});

$router->group(['prefix' => 'users'], function () use ($router)
{
    $router->post('', 'UserController@store');

    $router->group(['middleware' => 'auth:api'], function ($router)
    {
        $router->get('', 'UserController@index');
        $router->group(['prefix' => '{id}'], function () use ($router)
        {
            $router->get('', 'UserController@show');
            $router->put('', 'UserController@update');
            $router->delete('', 'UserController@delete');
        });
    });
});

$router->group(['prefix' => 'plans'], function () use ($router)
{
    $router->get('', 'PlanController@index');

    $router->group(['middleware' => 'auth:api'], function ($router)
    {
        $router->post('', 'PlanController@store');
        $router->group(['prefix' => '{id}'], function () use ($router)
        {
            $router->get('', 'PlanController@show');
            $router->put('', 'PlanController@update');
            $router->delete('', 'PlanController@delete');
        });
    });
});
