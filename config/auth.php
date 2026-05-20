<?php

return [
    'defaults' => [
        'guard' => 'pegawai',
        'passwords' => 'pegawai',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'pegawai' => [
            'driver' => 'session',
            'provider' => 'pegawai',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'pegawai' => [
            'driver' => 'eloquent',
            'model' => App\Models\Pegawai::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'pegawai' => [
            'provider' => 'pegawai',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
