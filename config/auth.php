<?php

return [
    'guards' => [
        'manager' => [
            'driver' => 'jwt',
            'provider' => 'manager',
            'hash' => false,
            'input_key' => 'access_token',
            'storage_key' => 'access_token',
        ],
    ],

    'providers' => [
        'manager' => [
            'driver' => 'eloquent',
            'model' => Team64j\LaravelManager\Models\User::class,
        ],
    ],
];
