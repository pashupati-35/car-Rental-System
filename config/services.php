<?php

return [


//    'google' => [
//        'client_id' => env('GOOGLE_CLIENT_ID'),
//        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
//        'redirect' => env('GOOGLE_REDIRECT_URI'),
//    ],
//    'facebook' => [
//        'client_id' => env('FACEBOOK_CLIENT_ID'),
//        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
//        'redirect' => env('FACEBOOK_REDIRECT_URI'),
//    ],

    'locationiq' => [
        'key' => env('LOCATIONIQ_API_KEY', 'your-default-key-here'),
    ],

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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
    ],

];
