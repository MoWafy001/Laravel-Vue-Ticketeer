<?php

return [

    'defaults' => [
        'guard' => 'organizer', // Default guard, though we'll be explicit mostly
        'passwords' => 'organizers',
    ],

    'guards' => [
        'organizer' => [
            'driver' => 'jwt',
            'provider' => 'organizers',
        ],

        'buyer' => [
            'driver' => 'jwt',
            'provider' => 'buyers',
        ],
    ],

    'providers' => [
        'organizers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Organizer::class,
        ],

        'buyers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Buyer::class,
        ],
    ],

    'passwords' => [
        'organizers' => [
            'provider' => 'organizers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'buyers' => [
            'provider' => 'buyers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
