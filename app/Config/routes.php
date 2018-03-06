<?php

$router->get('/(:id)/name/(:all?)',
    'App\Demo\DemoController@index')->middleware('t2', 't3');

/*
 *$router->group(['auth'], function($router) {
 *    $router->get('/(:id)/name/(:all?)',
 *        'App\Demo\DemoController@index')->middleware('t3');
 *});
 */
