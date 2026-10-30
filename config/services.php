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
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),

        // Redirect default otomatis menyesuaikan APP_URL jika variabel env belum diisi di VPS.
        'redirect' => env('GOOGLE_STUDENT_REDIRECT_URI') ?: (rtrim(env('APP_URL', 'http://localhost:8000'), '/') . '/auth/google/student/callback'),

        'student_redirect' => env('GOOGLE_STUDENT_REDIRECT_URI') ?: (rtrim(env('APP_URL', 'http://localhost:8000'), '/') . '/auth/google/student/callback'),
        'admin_redirect' => env('GOOGLE_ADMIN_REDIRECT_URI') ?: (rtrim(env('APP_URL', 'http://localhost:8000'), '/') . '/auth/google/admin/callback'),
    ],

];
