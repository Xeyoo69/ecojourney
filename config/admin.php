<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Account
    |--------------------------------------------------------------------------
    |
    | Used by DatabaseSeeder to create the dedicated admin account. The
    | password must be provided via .env; when empty the seeder skips
    | creating the admin account entirely.
    |
    */

    'email' => env('ADMIN_EMAIL', 'admin@ecojourney.com'),

    'password' => env('ADMIN_PASSWORD'),

];
