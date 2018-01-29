<?php
use Rest\Services;

return [
    'timezone' => 'PRC',

    // Services providers (DI)
    'providers' => [
        'request' => function() {
            return new \Rest\Http\Request;
        },

        'response' => function() {
            return new \Rest\Http\Response(200, ['Content-type: application/json;charset=utf-8'],
                Services::request()->params('format'));
        },

        'router' => function() {
            return new \Rest\Router;
        },

        'exceptions' => function() {
            return new \Rest\Exceptions(Services::response());
        }
    ],

    // Router placeholders
    'placeholders' => [
        ':id'  => '[0-9]+'
    ]
];
