<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Roles & permissions (Spatie Permission)
    |--------------------------------------------------------------------------
    |
    | When disabled, permission middleware and Filament role management are
    | skipped. Migrations still create permission tables so you can turn
    | this on later without schema changes.
    |
    */

    'permissions' => [
        'enabled' => env('FEATURE_PERMISSIONS_ENABLED', true),
    ],

];
