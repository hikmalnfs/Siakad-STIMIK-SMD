<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'mahasiswa' => [
            'driver' => 'session',
            'provider' => 'mahasiswas',
        ],
        'dosen' => [
            'driver' => 'session',
            'provider' => 'dosens',
        ],
        'dosenwali' => [
            'driver' => 'session',
            'provider' => 'dosens',
        ],
    ],
        'akademik' => [
            'driver' => 'session',
            'provider' => 'akademiks',
        ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'mahasiswas' => [
            'driver' => 'eloquent',
            'model' => App\Models\Mahasiswa::class,
        ],
        'dosens' => [
            'driver' => 'eloquent',
            'model' => App\Models\Dosen::class,
        ],
    ],
        'akademiks' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
            'conditions' => ['type' => 1], // Hanya user dengan type 1 (Departement Akademik)
        ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'mahasiswas' => [
            'provider' => 'mahasiswas',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'dosens' => [
            'provider' => 'dosens',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'akademiks' => [
            'provider' => 'akademiks',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => 10800,

];