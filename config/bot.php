<?php

return [
    /**
     * Bot token
     *
     * @link https://core.telegram.org/bots#generating-an-authorization-token
     */
    'token' => env('BOT_TOKEN', ''),

    'username_fallback' => env('BOT_USERNAME_FALLBACK', 'sda_church_bot'),

    'admin_usernames' => env('BOT_ADMIN_USERNAMES', ''),

    /**
     * SDA objects API url
     */
    'storage_api' => 'https://api.adventist.ua',

    /**
     * Users default language
     */
    'default_lang' => 'uk',

    /**
     * Patches file location
     */
    'patches_file' => database_path('source/church_patches.csv'),

    /**
     * Search by location in given radius
     */
    'search_radius' => 100000, // 100 km in meters

    'support' => [
        'channel' => [
            'name' => env('BOT_SUPPORT_CHANNEL_NAME', 'Telegram Support'),
            'link' => env('BOT_SUPPORT_CHANNEL_LINK', '')
        ]
    ],
];
