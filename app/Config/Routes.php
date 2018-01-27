<?php

$router->get('/(:id?)', 'App\Controllers\Demo@index');

$router->get('/search/(:keyword?)', 'App\Controllers\Search@index');
