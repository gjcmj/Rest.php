<?php
use Rest\Services;

return [
    'timezone' => 'PRC',

    // Custom Services providers (DI)
    'providers' => [],

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
    ],

    'middleware' => [
        \App\Middleware\Test::class
    ],

    'routeMiddleware' => [
        't2' => \App\Middleware\T2::class,
        't3' => \App\Middleware\T3::class,
        'test' => \App\Middleware\Test::class
    ],

    'middlewareGroups' => [
        'auth' => [
            \App\Middleware\Test::class,
            \App\Middleware\T2::class
        ]
    ]
];
