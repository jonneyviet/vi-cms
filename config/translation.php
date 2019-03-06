<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Translation Mode
    |--------------------------------------------------------------------------
    |
    | This option controls the translation's bundle mode of operation.
    |
    | Supported:
    |
    |   'database'      Use the database as the exclusive source for language entries.
    |   'files'         Use files as the exclusive source for language entries [Laravel's default].
     */
    'source'            => env('TRANSLATION_SOURCE', 'files'),

    // In case the files source is selected, please enter here the supported locales for your app.
    // Ex: ['en', 'es', 'fr']
    'available_locales' => [
        "en"    =>  ['id'=>'en','name'     =>  'English'],
        "vi"    =>  ['id'=>'vi','name'     =>  'Vietnamese']
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Translation Cache
    |--------------------------------------------------------------------------
    |
    | Choose whether to leverage Laravel's cache module and how to do so.
    |
    |   'enabled'       Boolean value.
    |   'timeout'       In minutes.
    |
     */
    'cache'             => [
        'enabled' => env('TRANSLATION_CACHE_ENABLED',false),
        'timeout' => env('TRANSLATION_CACHE_TIMEOUT',60),
        'suffix'  => env('TRANSLATION_CACHE_SUFFIX', 'translation'),
    ]
];
