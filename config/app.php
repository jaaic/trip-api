<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application ID
    |--------------------------------------------------------------------------
    |
    | This must be match with key's prefix in .config.ini
    |
    */

    'app_id' => 'TRIP_API',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => config('TRIP_API.KEY', 'SomeRandomString!!!'),

    'cipher' => 'AES-256-CBC',
];
