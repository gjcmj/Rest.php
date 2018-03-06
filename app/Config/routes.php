<?php

$router->get('/(:id)/name/(:all?)',
    'App\Demo\DemoController@index')->middleware('before', 'after');

$router->group(['auth'], function($router) {

    $router->get('/test', 'App\Demo\DemoController@group');
});
