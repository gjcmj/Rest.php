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

    // Exception Response Format
    'outputCallbackException' => function($message) {
        $isCustom = is_numeric(substr($message, 0, 3));

        return [
            'code' => $isCustom ? substr($message, 4, 5) : -1,
            'message' => $isCustom ? substr($message, 10) : $message
        ];
    },

    // Router placeholders
    'placeholders' => [
        ':id'  => '[0-9]+'
    ]
];
