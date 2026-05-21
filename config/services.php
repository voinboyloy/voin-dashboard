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

    'notion' => [
        'token' => env('NOTION_TOKEN'),
        'databases' => [
            'tasks' => env('NOTION_TASKS_DATABASE_ID'),
            'time_blocks' => env('NOTION_TIME_BLOCKS_DATABASE_ID'),
            'daily_reviews' => env('NOTION_DAILY_REVIEWS_DATABASE_ID'),
            'transactions' => env('NOTION_TRANSACTIONS_DATABASE_ID'),
            'wishlist' => env('NOTION_WISHLIST_DATABASE_ID'),
            'subscriptions' => env('NOTION_SUBSCRIPTIONS_DATABASE_ID'),
            'habits' => env('NOTION_HABITS_DATABASE_ID'),
            'workout_plans' => env('NOTION_WORKOUT_PLANS_DATABASE_ID'),
            'exercises' => env('NOTION_EXERCISES_DATABASE_ID'),
            'credentials' => env('NOTION_CREDENTIALS_DATABASE_ID'),
            'notes' => env('NOTION_NOTES_DATABASE_ID'),
            'events' => env('NOTION_EVENTS_DATABASE_ID'),
        ],
    ],

];
