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
    'telegram-bot-api'=>[
        // 'token'=>env('TELEGRAM_BOT_TOKEN','5109079500:AAFE0Sa7rZ5SMWMPlYVr9pmbObfwQicWf_c')
        // 'token'=>env('TELEGRAM_BOT_TOKEN','6039785219:AAFHbLMtSS2b7n4_fBF8pOX_etxpjUKHLaI')
        'token'=>env('TELEGRAM_BOT_TOKEN','5109079500:AAFE0Sa7rZ5SMWMPlYVr9pmbObfwQicWf_c')


    ],
    'telegram_id'=>env('TELEGRAM_BOT_TOKEN','-1001631174179'),
    // chat attendance id
    // 'telegram_id'=>env('TELEGRAM_BOT_TOKEN','-1001638066433'),
    // PostingFB Channer
    // 'telegram_id'=>env('TELEGRAM_BOT_TOKEN','-1001890162423'),
    // banban notification
    // 'telegram_id'=>env('TELEGRAM_BOT_TOKEN','-1001631174179'),
    // default 
    // 'telegram_id'=>env('TELEGRAM_BOT_TOKEN','652747865'),
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
