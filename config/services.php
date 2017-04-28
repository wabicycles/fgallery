<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun'  => [
        'domain' => env('MAILGUN_DOMAIN', ''),
        'secret' => env('MAILGUN_SECRET', ''),
    ],
    'mandrill' => [
        'secret' => env('MANDILL_SECRET', ''),
    ],
    'ses'      => [
        'key'    => '',
        'secret' => '',
        'region' => 'us-east-1',
    ],
    'stripe'   => [
        'model'  => App\User::class,
        'key'    => '',
        'secret' => '',
    ],
    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID', ''),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', ''),
        'redirect'      => env('APP_URL') . '/auth/facebook/callback',
    ],
    'google'   => [
        'client_id'     => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'redirect'      => env('APP_URL') . '/auth/google/callback',
    ],
    'twitter'  => [
        'client_id'     => env('TWITTER_CLIENT_ID', ''),
        'client_secret' => env('TWITTER_CLIENT_SECRET', ''),
        'redirect'      => env('APP_URL') . '/auth/twitter/callback',
    ],
    'google_maps' => [
        'key' => env('GOOGLE_MAP_KEY', '')
    ]
];
