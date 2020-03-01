<?php

return [
    /**
     * Bot token
     *
     * @link https://core.telegram.org/bots#generating-an-authorization-token
     */
    'token' => env('BOT_TOKEN', ''),

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
];
