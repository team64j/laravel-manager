<?php

return [
    'guards' => [
        'manager' => [
            'driver' => 'jwt',
            'provider' => 'users',
            'hash' => false,
            'input_key' => 'access_token',
            'storage_key' => 'access_token',
        ],
    ],
];
