<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    
    'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT'),
],

'stripe' => [
    'key'             => env('STRIPE_KEY'),
    'secret'          => env('STRIPE_SECRET'),
    'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET'),
    'currency'        => env('STRIPE_CURRENCY', 'pkr'),
    'commission_rate' => (float) env('PLATFORM_COMMISSION_RATE', 0.10),
    'connect_country' => env('STRIPE_CONNECT_COUNTRY', 'US'),

    // Checkout charges in `currency`, but the platform account settles into
    // `payout_currency` (USD), so transfers go out converted at a fixed rate.
    'payout_currency' => env('STRIPE_PAYOUT_CURRENCY', env('STRIPE_CURRENCY', 'pkr')),
    'fx_pkr_to_usd'   => (float) env('STRIPE_FX_PKR_TO_USD', 0.0036),

    // The Accounts v2 / AccountLinks v2 endpoints are only available on a
    // preview API version. v1 calls (Checkout, PaymentIntents) use the SDK
    // default and must NOT send this.
    'preview_version' => env('STRIPE_PREVIEW_VERSION', '2026-07-29.preview'),
],

];
